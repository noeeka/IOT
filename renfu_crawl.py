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
import requests
import os
import zipfile


#########定义通用方法#########
def mkdir(path):
    path = path.strip()
    path = path.rstrip("\\")
    isExists = os.path.exists(path)
    if not isExists:
        os.makedirs(path)
        return path
    else:
        return False


# 获取上一次插入ID服务
def getLastId(db):
    db.execute("select last_insert_id() as lid;")
    data = db.fetchall()
    return data[0]['lid']


# 下载图片服务
def downloadImage(imgUrl, savePath):
    mkdir(savePath)
    imgUrl = imgUrl.replace("\\", "/")
    local_filename = imgUrl.split('/')[-1]
    r = requests.get(imgUrl, stream=True)
    counter = 0
    if not savePath.endswith("/"):
        savePath += "/"
    f = open(savePath + local_filename, 'wb')
    for chunk in r.iter_content(chunk_size=8192):
        if chunk:
            f.write(chunk)
            f.flush()
            counter += 1
    f.close()
    return savePath + local_filename


# 获取文件创建时间服务
def get_FileCreateTime(filePath):
    t = os.path.getctime(filePath)
    return t


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
            identify = t['IDENTITY']
            mobile = t['PHONE']
            name = t['NAME']
            age = t['AGE']
            address = t['ADDRNOW']
            career = t['PROFESSION']
            nationality = t['NATION']
            birthplace = t['ADDRBIRTH']

            if t['SEX'] == "女":
                sex = 1
            else:
                sex = 0
            sql = "SELECT `customer_no` FROM `patient_info` WHERE `customer_no`='" + str(t['NO']) + "'"
            cursor.execute(sql)
            result_check_patient = cursor.fetchone()
            if result_check_patient == None:
                sql_patient = "INSERT INTO `patient_info` SET `name`='" + str(name) + "',`sex`=" + str(
                    sex) + ",`customer_no`='" + str(t['NO']) + "',`age`=" + str(age) + ",`identify`='" + str(
                    identify) + "',`callphone`='" + str(mobile) + "',`address`='" + str(address) + "',`career`='" + str(
                    career) + "',`nationality`='" + str(nationality) + "',`birthplace`='" + str(birthplace) + "'"
                try:
                    cursor.execute(sql_patient)
                    connection.commit()
                    patient_id = getLastId(cursor)
                except:
                    print(sql_patient)

            patient_info_sql = "SELECT * FROM `patient_info` WHERE `customer_no`='" + str(t['NO']) + "'"
            cursor.execute(patient_info_sql)
            result_patient_info = cursor.fetchone()

            cursor.execute("SELECT * FROM `batch` WHERE `datetime`=" + str(int(time.mktime(
                time.strptime(str(t['COMMIT_TIME']), "%Y/%m/%d %H:%M:%S")))) + " AND `patient_info_id`=" + str(
                result_patient_info['id']))
            result_check_batch = cursor.fetchone()
            # 插入检查批次表
            # 插入之前先检查检查时间和患者ID是否已存在，若存在不插入
            if result_check_batch == None:
                # 这里不用patientid=getLastId(cursor)的原因是如果病人已经存在，但是检查批次不一样，就不需要再次插入病人表，直接将病人id从表中查出在插入检查批此表即可
                sql_batch = "INSERT INTO `batch` SET `patient_info_id`=" + str(
                    result_patient_info['id']) + ",`datetime`=" + str(
                    int(time.mktime(time.strptime(str(t['COMMIT_TIME']), "%Y/%m/%d %H:%M:%S")))) + ",`status`=0"
                cursor.execute(sql_batch)
                connection.commit()
                batch_id = getLastId(cursor)
                # 插入影像文件表
                file_path = downloadImage("http://www.medtmt.net:8090/upload" + t['DATA_PATH'], t['NO'])
                mkdir(str(int(time.mktime(time.strptime(str(t['COMMIT_TIME']), "%Y/%m/%d %H:%M:%S")))))
                f = zipfile.ZipFile(file_path, 'r')
                for file in f.namelist():
                    f.extract(file, str(int(time.mktime(time.strptime(str(t['COMMIT_TIME']), "%Y/%m/%d %H:%M:%S")))))
                    cursor.execute(
                        "INSERT INTO `image_info` SET `batch_id`=" + str(batch_id) + ",`patient_info_id`=" + str(
                            result_patient_info['id']) + ",`taketime`=" + str(
                            get_FileCreateTime(str(int(time.mktime(time.strptime(str(t['COMMIT_TIME']), "%Y/%m/%d %H:%M:%S"))))+"/"+file)) + ",`filepath`='" + str(str(int(time.mktime(time.strptime(str(t['COMMIT_TIME']), "%Y/%m/%d %H:%M:%S"))))) + "/" + str(file) + "'")
            reportInfos = content.find('reportinfos')['pdfsrc']
            # if content.find('reportinfos')['pdfsrc']==
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
