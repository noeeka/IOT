from suds.client import Client
from suds.xsd.doctor import ImportDoctor, Import
import pymysql.cursors
import urllib3
from bs4 import BeautifulSoup as BS
import urllib3
import sys
import os
import base64
import uuid
import time
import datetime
import xmltodict
import json



#########定义通用方法#########
def mkdir(path):
    path = path.strip()
    path = path.rstrip("\\")
    isExists = os.path.exists(path)
    if not isExists:
        os.makedirs(path)
        return True
    else:
        return False


def getLastId(db):
    db.execute("select last_insert_id() as lid;")
    data = db.fetchall()
    return data[0]['lid']


# 连接数据库服务
connection = pymysql.connect(host='127.0.0.1',
                             user='root',
                             password='root',
                             db='kangkang',
                             charset='utf8mb4',
                             cursorclass=pymysql.cursors.DictCursor)

try:
    with connection.cursor() as cursor:
        imp = Import("http://www.w3.org/2001/XMLSchema", location="http://www.w3.org/2001/XMLSchema.xsd")
        imp.filter.add("http://WebXml.com.cn/")
        doctor = ImportDoctor(imp)

        url = 'http://www.medtmt.net:8090/infosysservice/Service1.asmx?wsdl'
        client = Client(url, doctor=doctor)
        # 获取诊断报告服务（包含诊断信息，病人信息和病例）
        raw = client.service.GetReport_RF('83e0df25fdc5f539abf89cca69e773f7')
        for t in eval(raw):
            http = urllib3.PoolManager()
            r = http.request('GET', "http://www.medtmt.net:8090/upload" + str(t['REPORT_FILE_PATH']))
            content = BS(r.data.decode(), "lxml")
            project = content.find('userinfos')['projects']
            identify = content.find('userinfos')['card']
            mobile = content.find('userinfos')['mobile']
            name = content.find('userinfos')['user']
            age = content.find('userinfos')['age']
            if content.find('userinfos')['sex'] == "女":
                sex = 1
            else:
                sex = 0
            mkdir("./" + str(t['CUSTOMER_NO']))
            sql = "SELECT `customer_no` FROM `patient_info` WHERE `customer_no`='" + str(t['CUSTOMER_NO']) + "'"
            cursor.execute(sql)
            result = cursor.fetchone()
            if result == None:
                sql_patient = "INSERT INTO `patient_info` SET `name`='" + str(
                    t['CUSTOMER_NAME']) + "',`sex`=" + str(sex) + ",`customer_no`='" + str(
                    t['CUSTOMER_NO']) + "',`age`=" + str(
                    t['CUSTOMER_AGE']) + ",`identify`='" + str(identify) + "',`callphone`='" + str(
                    t['CUSTOMER_PHONE']) + "',`address`=''"
                try:
                    cursor.execute(sql_patient)
                    connection.commit()
                    patient_id = getLastId(cursor)
                except:
                    print(sql_patient)

            patient_info_sql = "SELECT * FROM `patient_info` WHERE `customer_no`='" + str(t['CUSTOMER_NO']) + "'"
            cursor.execute(patient_info_sql)
            result_patient_info = cursor.fetchone()
            sql_batch = "INSERT INTO `batch` SET `patient_info_id`=" + str(
                result_patient_info['id']) + ",`datetime`=" + str(
                int(time.mktime(time.strptime(str(t['COMMIT_TIME']), "%Y/%m/%d %H:%M:%S")))) + ",`status`=0"
            cursor.execute(sql_batch)
            connection.commit()
            batch_id = getLastId(cursor)
            if content.findChildren('data') != []:
                for item in content.findChildren('data'):
                    img = base64.b64decode(item.string)
                    filepath = "./" + str(t['CUSTOMER_NO']) + "/" + str(uuid.uuid1()) + ".jpg"
                    file = open(filepath, 'wb')
                    file.write(img)
                    file.close()
                    sql_image = "INSERT INTO `image_info` SET `patient_info_id`=" + str(
                        patient_id) + ",`taketime`=0,`filepath`='" + str(
                        filepath) + "'"
                    try:
                        cursor.execute(sql_image)
                        connection.commit()
                    except:
                        print(sql_image)

            reportInfos = content.find('reportinfos')['pdfsrc']
            #if content.find('reportinfos')['pdfsrc']==
            sql_report = "INSERT INTO `report` SET `patient_info_id`=" + str(
                result_patient_info['id']) + ",`content`='" + str(content.find('reportinfos')) + "',`filepath`='" + str(
                reportInfos) + "',`batch_id`=" + str(batch_id)
            try:
                cursor.execute(sql_report)
                connection.commit()
            except:
                print(sql_report)

finally:
    connection.close()
