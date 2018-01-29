import socket
import sys
import pymysql

print('hello')
HOST, PORT = '', #port
sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
sock.bind((HOST, PORT))
sock.listen(1)
conn, addr = sock.accept()

mysql_conn = pymysql.connect(host='127.0.0.1', user='', password='', db='', charset='utf8')
curs = mysql_conn.cursor()
insert_q = "insert into 'tableName' ('tableFields') values ('reciveField')"

while True:
	data = conn.recv(1024)
	sys.stdout.write("Recive: "); print(data);
	curs.execute(insert_q,(data.split(',')[0], data.split(',')[1], data.split(',')[2]))
	mysql_conn.commit()
conn.close()
mysql_conn.close()
