#!/usr/bin/env python3
import os
import time
from dotenv import load_dotenv

load_dotenv()

os.popen('docker-compose up --build -d').read()

seconds = 30
while seconds > 0:
    time.sleep(1)
    os.system(f'echo "Criando container {seconds}s"')
    seconds = seconds - 1

MYSQL_DATABASE = os.getenv('MYSQLDB')
MYSQL_ROOT_PASSWORD = os.getenv('MYSQLROOTPWD')
MYSQL_USER = os.getenv('MYSQLUSR')
MYSQL_PASSWORD = os.getenv('MYSQLPWD')

accessDb = f"docker exec db"

os.system(f'echo "Garantindo previlégios"')

os.system(f"{accessDb} mysql -u {MYSQL_USER} -p {MYSQL_PASSWORD} | use {MYSQL_DATABASE}; GRANT select, update, insert, delete, alter, create, drop ON .* TO {MYSQL_USER}@localhost IDENTIFIED BY '{MYSQL_PASSWORD}'; FLUSH PRIVILEGES; QUIT;")

os.system(f"{accessDb} mysql {MYSQL_DATABASE} < sql/props-mysql.sql")