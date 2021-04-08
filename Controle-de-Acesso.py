#!/usr/bin/python3

# Bibliotecas para configuracao de tempo
from time import time, sleep
from datetime import datetime

# Biblioteca para tratamento dos sinais enviados pelo sistema operacional da BBB
import signal

# Bibliotecas para uso do serial
import serial
import Adafruit_BBIO.UART as UART

# Biblioteca para uso dos pinos da BBB
import Adafruit_BBIO.GPIO as GPIO

# Biblioteca para execucao de um cliente MQTT
import paho.mqtt.client as paho

# Biblioteca para conexao ao MySQL
import mysql.connector as mysql

# Bibliotecas para uso do LCD
import board
import digitalio
import adafruit_character_lcd.character_lcd as characterlcd

# CONSTANTES:

# DISPOSITIVO
BBB_operacao = 1 # 0 para atuar somente como servidor, 1 como srevidor e dispositivo de acesso tbm
BBB_area = "BlocoM"
BBB_sala = "M204"

# Configuracao do LCD
lcd_columns = 16
lcd_rows = 2
lcd_d7 = digitalio.DigitalInOut(board.P8_8)
lcd_d6 = digitalio.DigitalInOut(board.P8_10)
lcd_d5 = digitalio.DigitalInOut(board.P8_12)
lcd_d4 = digitalio.DigitalInOut(board.P8_14)
lcd_en = digitalio.DigitalInOut(board.P8_16)
lcd_rs = digitalio.DigitalInOut(board.P8_18)
lcd = characterlcd.Character_LCD_Mono(lcd_rs, lcd_en, lcd_d4, lcd_d5, lcd_d6, lcd_d7, lcd_columns, lcd_rows)

# Funcao para exibicao de texto no LCD
def LCD_txt(frase, espera = 0):
    lcd.clear()
    lcd.message = frase
    sleep(espera)
    
LCD_txt('INICIANDO A\nROTINA')

def handle_iterrupt():
      LCD_txt('SERVICO PARADO')
      sys.exit(0)

signal.signal(signal.SIGTERM, handle_iterrupt)

# Constantes do cliente MQTT
mqtt_broker = "192.168.0.105" 
mqtt_port = 1883                                      
mqtt_user = "debian"
mqtt_password = "temppwd"
mqtt_clientID = "M207"
mqtt_sub = "ControleDeAcesso/#"

# Constantes do cliente SQL
sql_user = 'debian'
sql_pwd = 'temppwd'
sql_host = 'localhost'
sql_db = 'Registro'

# Configuracao dos demais pinos LEDS
pino_neg = "P8_15"
pino_lib = "P8_17"
pino_porta = "P8_9"
pino_botao = "P8_7"
GPIO.setup(pino_neg, GPIO.OUT)
GPIO.setup(pino_lib, GPIO.OUT)
GPIO.setup(pino_porta, GPIO.OUT)
GPIO.setup(pino_botao, GPIO.IN, pull_up_down=GPIO.PUD_UP)
estado_porta = GPIO.LOW 

# Rotina a ser executada quando uma interrupcao for detectada
def interrupt_callback (pino_botao):
    estado_porta = GPIO.input(pino_porta)
    estado_porta = GPIO.HIGH if estado_porta == GPIO.LOW else GPIO.LOW
    GPIO.output(pino_porta, estado_porta)
    # print ("interrupt")
    return None

# Configuracao do evento de interrupcao do botao
GPIO.add_event_detect(pino_botao, GPIO.FALLING)
GPIO.add_event_callback(pino_botao, callback=interrupt_callback,bouncetime=200)

# Inicializacao da porta serial UART1 da BB
UART.setup("UART1")
ser = serial.Serial(port = "/dev/ttyS1", baudrate=9600)
ser.close()
ser.open()
sleep(0.2)

# Conexao ao banco de dados MySQL
db = mysql.connect(user=sql_user, password=sql_pwd, host=sql_host, database=sql_db)
sql_cursor = db.cursor() # Get a cursor

# FUNCOES -------------------------------------------------------------------------
# Retorna um numero especificado de caracteres do lado esquerdo de uma string
def left(s, amount):
    return s[:amount]

# Retorna um numero especificado de caracteres do lado direito de uma string
def right(s, amount):
    return s[-amount:]

# Retorna um numero especificado de caracteres do meio de uma string
def mid(s, offset, amount):
    return s[offset:offset+amount]

# Funcao de leitura e decodificacao da tag RFID
def RFID (rfid_payload, formato):
    a=datetime.now()
    rfid_hex = ''
    rfid_dec = ''
    rfid_chr = ''
    sucesso = 0
    for c in rfid_payload:
        if rfid_hex == '' :
            aux = ''
        else:
            aux = ' '
        rfid_hex = rfid_hex + aux + hex(int(c))
        rfid_dec = rfid_dec + aux + str(c)
        #rfid_chr = rfid_chr + str(c)
    if (rfid_dec[0] == '2' and rfid_dec[len(rfid_dec) - 1] == '3'): # se a msg foi recebida propriamente
        sucesso = 1
        rfid_chr = rfid_payload[1:11]
        rfid_chr = rfid_chr.decode('utf-8')
        if formato == 'CHR':
                RX_tag_id = str(rfid_chr)
        elif formato == 'HEX':
                RX_tag_id = rfid_hex[5:54]
        elif formato == 'DEC':
                RX_tag_id = rfid_dec[3:32]
        else:
            # print ("Escolha um formato adequado. Use 'HEX' para hexadecimal, 'DEC' para decimal e 'CHR' para caracteres ascii.")
            RX_tag_id = 'Erro!'
    return sucesso, RX_tag_id

# Query de busca de permissao da tag no banco de dados
def sql_busca (RX_tag_id, RX_area, RX_sala):
    # print("area " + RX_area)
    # print("sala " + RX_sala)
    query_acesso = "SELECT inicio_vigencia, fim_vigencia FROM Acesso \
           WHERE tag_id = '" + RX_tag_id + "' AND (area = '" + RX_area + "' OR area = '*')  AND (sala = '" + RX_sala + "' OR sala = '*');"
    sql_cursor.execute(query_acesso)
    res_acesso = sql_cursor.fetchone()
    # print (str(res_acesso))
    query_cadastro = "SELECT matricula FROM Cadastro WHERE tag_id = '" + RX_tag_id + "';"
    sql_cursor.execute(query_cadastro)
    res_cadastro = sql_cursor.fetchone()
    # status_acesso = 0 : com acesso com cadastro 
    # status_acesso = 1 : com acesso sem cadastro
    # status_acesso = 2 : sem acesso com cadastro 
    # status_acesso = 3 : sem acesso sem cadastro
    # status_acesso = 4 : CADASTRO ON
    # status_acesso = 5 : CADASTRO OK
    # status_acesso = 6 : CADASTRO OFF
    matricula = str(res_cadastro[0]) if res_cadastro != None else ''
    if res_acesso != None : 
        # formatacao do horario
        inicio = datetime.strptime(str(res_acesso[0]), '%d-%m-%Y %H:%M:%S')
        final = datetime.strptime(str(res_acesso[1]), '%d-%m-%Y %H:%M:%S')
        hoje = datetime.now().replace(microsecond=0)
        access = "LIBERADO" if (inicio < hoje and hoje < final) else "NEGADO"
    else:
        access = "NEGADO"
        
    if access == "LIBERADO" and matricula != '': # com acesso e com cadastro
        status_acesso = 0
        # print ("COM ACESSO COM CADASTRO") 
        resposta = RX_tag_id + "/" + matricula + "/" + access
    elif access == "LIBERADO" and matricula == '': # com acesso sem cadastro 
        status_acesso = 1
        # print ("COM ACESSO SEM CADASTRO") 
        resposta = RX_tag_id + "/TEMPORARIO/" + access
    elif access == "NEGADO" and matricula != '': # sem acesso com cadastro 
        status_acesso = 2
        # print ("SEM ACESSO COM CADASTRO") 
        resposta = RX_tag_id + "/" + matricula + "/" + access
    elif access == "NEGADO" and matricula == '': # sem cadastro sem acesso
        # print ("SEM ACESSO SEM CADASTRO") # sem acesso com ou sem cadastro
        status_acesso = 3
        resposta = RX_tag_id + "/SEMACESSO/NEGADO"
    return resposta, status_acesso, matricula

# Insere a solicitacao de acesso no banco de dados
def sql_registro (tempo, RX_tag_id, RX_area, RX_sala, status_acesso, RX_porta):
    log_entry = "INSERT INTO RegistroLog (hora,tag_id,area,sala,acesso,entrada) VALUES(%s, %s, %s, %s, %s, %s);"
    val = (tempo, RX_tag_id, RX_area, RX_sala, status_acesso, RX_porta)
    sql_cursor.execute(log_entry, val)
    db.commit()
    # print(sql_cursor.rowcount, "record inserted.")
    return None

# Callback executada quando o cliente MQTT se conecta ao servidor
def on_connect(client, userdata, flags, rc): #(rc = return code)
    if rc==0:
        # print("Connected! Returned code=", rc)
        client.subscribe(mqtt_sub)
        #client.message_callback_add(mqtt_sub, on_message)
    else:
        # print("Bad connection Returned code= ", rc)
        pass
    return None

# Callback executada quando o cliente publica uma mensagem
# def on_publish(client,userdata,result):         
#     #print("on publish \n")
#     return None

# Callback executada quando o cliente MQTT recebe uma mensagem
def on_message(client, userdata, msg):
    RX_topic = str(msg.topic)
    RX_msg = str(msg.payload.decode('utf-8'))
    # print( "Rx topic: " + RX_topic)
    # print( "Rx msg: " + RX_msg)
    if right(RX_topic,6) == "acesso":
        #definicao do topico de resposta: (assunto muda de 'acesso' para 'permissao')
        TX_topic = left(RX_topic, len(RX_topic) - 6) + "permissao"
        # print( "tx topic: " + TX_topic)
        c0 = RX_topic.find("/area/", 0)
        c1 = RX_topic.find("/sala/", c0 + 1) #contagem comeca em 0
        RX_area  = mid(RX_topic, c0 + 6 , c1 - (c0 + 6))
        RX_sala = mid(RX_topic, c1 + 6, len(RX_topic) - (c1 + 6) - 7)
        # segmentacao da resposta
        c2 = RX_msg.find("/", 0)
        c3 = RX_msg.find("/", c2 + 1)
        RX_tag_id = mid(RX_msg, 0, c2)
        RX_porta = mid(RX_msg, c2 + 1, c3 - 1 - c2)
        RX_tempo = mid(RX_msg, c3 + 1, len(RX_msg) - c3 - 1) 
        # print ( "id= " + RX_tag_id + " porta= " + RX_porta + " tempo= " + RX_tempo)
        # query cadastro AQUI 
        ret3 = sql_busca (RX_tag_id, RX_area, RX_sala)
        resposta = str(ret3[0])+ "o"
        status_acesso = ret3[1]
        # publicar mensagem
        mqttClient.publish(TX_topic,resposta)
        #inserir entrada no log de dados
        0 if RX_porta == "P0" else 1
        sql_registro (RX_tempo, RX_tag_id, RX_area, RX_sala, status_acesso, RX_porta)
    return None

# Callback executada quando o cliente MQTT e disconectado do servidor
# def on_disconnect(client, userdata, rc):
#     # print ("disconneted")
#     pass
#     return None

# Inicializacao do cliente MQTT
mqttClient = paho.Client(mqtt_clientID)
# Atribuicao de callbacks
mqttClient.on_connect = on_connect
mqttClient.on_message = on_message
#mqttClient.on_publish = on_publish
#mqttClient.on_disconnect = on_disconnect
# Conexao ao servidor
mqttClient.username_pw_set(mqtt_user, mqtt_password)
mqttClient.connect(mqtt_broker, port=mqtt_port)

# Inicio do loop MQTT
mqttClient.loop_start()

# Rotina executada quando o acesso é liberado
def liberar(matricula, pino_neg=pino_neg, pino_lib=pino_lib, pino_porta=pino_porta):
    estado_porta == GPIO.HIGH
    # Acionamento LED
    GPIO.output(pino_neg, GPIO.LOW)
    GPIO.output(pino_lib, GPIO.HIGH)
    # Acionamento do atuador
    GPIO.output(pino_porta, GPIO.HIGH)
    setenca = 'ACESSO LIBERADO\n' + str(matricula)
    # print('matricula : ' + matricula)
    LCD_txt(setenca, 3)
    GPIO.output(pino_lib, GPIO.LOW)
    return estado_porta

# Rotina executada quando o acesso é negado
def negar(pino_neg=pino_neg, pino_lib=pino_lib, pino_porta=pino_porta):
    estado_porta == GPIO.LOW
    # Acionamento LED
    GPIO.output(pino_lib, GPIO.LOW)
    GPIO.output(pino_neg, GPIO.HIGH)
    setenca = 'ACESSO NEGADO\n' + str(matricula)
    LCD_txt(setenca, 3)
    GPIO.output(pino_neg, GPIO.LOW)
    return estado_porta
    
# Loop principal do código
try:
    anterior = ''
    if BBB_operacao == 0 :
        while True:
            pass
    elif BBB_operacao == 1:
        millis = datetime.now()
        while True:
            LCD_txt('SALA ' + BBB_sala +'\nAPRESENTE CARTAO') 
            if ser.isOpen():
                rfid_payload = ser.read(14)
                #tempo = datetime.now().strftime("%d-%m-%Y %H:%M:%S")
                #print('tempo:' + tempo)
                retorno = RFID(rfid_payload,'CHR')
                if retorno[0] == 1: # se sucesso
                    RX_tag_id = retorno[1]
                    c = datetime.now() - millis
                    if RX_tag_id != anterior or (RX_tag_id == anterior and c.seconds > 4 ):
                        # print (RX_tag_id)
                        anterior = RX_tag_id
                        estado_porta = GPIO.input(pino_porta)
                        porta = 'P0' if estado_porta == GPIO.LOW else 'P1'
                        ret1 = sql_busca (RX_tag_id, BBB_area, BBB_sala)
                        status_acesso = ret1[1]
                        matricula = ret1[2]
                        tempo = datetime.now().strftime("%d-%m-%Y %H:%M:%S")
                        sql_registro (tempo, RX_tag_id, BBB_area, BBB_sala, status_acesso, porta)
                        if status_acesso == 0 or status_acesso == 1:
                            liberar(matricula)
                        elif status_acesso == 2 or status_acesso == 3:
                            negar()
                        millis = datetime.now()
                        ser.reset_input_buffer()
            else:
                    pass
    
    mqttClient.loop_stop()

# Rotina de tratamento caso o programa serja interrompido
except KeyboardInterrupt:
    LCD_txt("CODIGO PARADO\nREINICIAR!")
    GPIO.output(pino_porta, GPIO.LOW)
    # print ('Caught KeyboardInterrupt')
