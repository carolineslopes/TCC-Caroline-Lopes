// DECLARAÇÃO DAS BIBLIOTECAS:
#include <UIPEthernet.h>     //Biblioteca para o módulo ENC28J60
#include <PubSubClient.h>    //Biblioteca para o protocolo MQTT 
#include <LiquidCrystal.h>   //Biblioteca para o display LCD 16x2
#include <SoftwareSerial.h>  //Biblioteca para a leitura de Serial
#include <RDM6300.h>         //Biblioteca para o módulo RDM6300 
#include <Wire.h>            //Biblioteca para o módulo DS1307
#include <RTClib.h>          //Biblioteca para o módulo DS1307

// Inicialização da biblioteca RTC:
RTC_DS1307 RTC;

// Inicialização do cliente Ethernet:
EthernetClient ethClient;

// Atribuição dos endereços MAC e IP ao módulo ENC28J60:
uint8_t myMAC[6] = {0x54, 0x55, 0x58, 0x10, 0x00, 0x24};
IPAddress myip(192, 168, 0, 109);

// Inicialização do cliente MQTT:
IPAddress mqttServer(192, 168, 0, 105);
void callback(char* rx_topic, byte* rx_payload, unsigned int rx_length);
PubSubClient mqttClient(mqttServer, 1883, callback, ethClient);
char MQTT_payload [34];
#define MQTT_CLIENT     "M205"
#define MQTT_USER       "debian"
#define MQTT_PWD        "temppwd"
#define MQTT_PUB        "ControleDeAcesso/area/BlocoM/sala/M205/acesso"
#define MQTT_SUB        "ControleDeAcesso/area/BlocoM/sala/M205/permissao"

// Tempo de espera de resposta a uma solicitação de acesso: 5 s
#define MQTT_WAIT1      5000

// Pinos de LEDs e Botão:
#define PINO_BOTAO     18
#define PINO_PORTA     22
#define PINO_LIBERADO  25
#define PINO_NEGADO    2

// Pinos do LCD:
#define LCD_RS 12
#define LCD_E 11
#define LCD_D4 3
#define LCD_D5 4
#define LCD_D6 5
#define LCD_D7 6

// Inicialização da biblioteca LiquidCrystal:
LiquidCrystal LCD(LCD_RS, LCD_E, LCD_D4, LCD_D5, LCD_D6, LCD_D7);

// Variáveis de estado do progama (temporizadores, contadores...)
#define WAIT0 0
#define WAIT1 1
int estado = WAIT0;
unsigned long temp_WAIT1;
int cont_espera = 0;
int estado_LCD0 = 1;
int estado_LCD1 = 1;
volatile int estado_porta = LOW;
volatile int estado_porta0 = HIGH;
String porta = "P0";
String TX_tag_id = ""; // numero da tag enviada

// Inicialização da biblioteca SoftwareSerial:
SoftwareSerial RFID(13, 10);
uint8_t RFID_payload[6];

// Inicialização da biblioteca RDM6300:
RDM6300 RDM6300(RFID_payload);

// Inicialização da funçao de exibição do LCD:
void LCDtxt (int x, String matricula = " ");

// Função de callback do cliente MQTT:
void callback(char* rx_topic, byte* rx_payload, unsigned int rx_length) {
  //Serial.println ("Callback:");
  String str_payload = "";
  // A mensagem recebida só será analisada caso o programa esteja a espera da resposta de uma solicitação:
  if (estado == WAIT1) {
    if (strcmp(rx_topic, MQTT_SUB) == 0) {
      for (int i = 0; i < rx_length; i++) {
        str_payload = str_payload + String((char)rx_payload[i]);
      }
      // EXEMPLO DE MENSAGEM RECEBIDA: 10BCAE6/20201EN20110/LIBERADO
      int cont_barra0 = str_payload.indexOf("/");
      int cont_barra1 = str_payload.indexOf("/", cont_barra0 + 1);
      String RX_tag_id = str_payload.substring(0, cont_barra0);
      String matricula = str_payload.substring(cont_barra0 + 1, cont_barra1 - 1);
      String permissao = str_payload.substring(cont_barra1 + 1, cont_barra1 + 4);
      //Serial.print("Mensagem recebida: ");
      //Serial.println(str_payload);

      // Caso o número da tag recebida seja idêntico ao número envidado:
      if (RX_tag_id == TX_tag_id) {
        if (permissao == "LIBERADO") {
          //Serial.println("ACESSO LIBERADO!");
          digitalWrite(PINO_LIBERADO, HIGH);
          LCDtxt (2, matricula); // Display: ACESSO LIBERADO
          estado_porta = HIGH;
          digitalWrite(PINO_LIBERADO, LOW);
        } else if (permissao == "NEGADO") {
          //Serial.println("ACESSO NEGADO!");
          digitalWrite(PINO_NEGADO, HIGH);
          LCDtxt (3); // Display: ACESSO NEGADO
          digitalWrite(PINO_NEGADO, LOW);
        }
        estado = WAIT0;
      }
    }
  }
  cont_espera = 0;
  discard();
}

void setup() {
  // Inicialização do LCD:
  LCD.begin(16, 2);
  LCDtxt(0); // Display: INICIANDO O DISPOSITIVO!

  // Inicialização do SerialMonitor:
  //Serial.begin(9600);

  // Configuração I/O dos pinos:
  pinMode(PINO_NEGADO, OUTPUT); //led vermelho
  pinMode(PINO_LIBERADO, OUTPUT); //led ver
  pinMode(PINO_PORTA, OUTPUT); //led ver
  pinMode(PINO_BOTAO, INPUT_PULLUP); //led ver

  // Configuração do interrupção:
  attachInterrupt(digitalPinToInterrupt(PINO_BOTAO), interrupcao, FALLING);

  // Inicialização do módulo RTC:
  Wire.begin();
  RTC.begin();

  // Inicialização do serial do RFID:
  RFID.begin(9600);

  // Conexão do cliente Ethernet:
  Ethernet.begin(myMAC, myip);

  // Conexão ao servidor MQTT:
  mqtt_connexao();
}

void loop() {
  // Exibe a mensagem padrão do sistema:
  if (estado_LCD0 != estado_LCD1) {
    LCDtxt (1); // Display: APRESENTE CARTAO
  }

  if (estado_porta0 != estado_porta) {
    digitalWrite(PINO_PORTA, estado_porta);
  }

  // Caso o programa esteja a espera da leitura de uma nova tag:
  if (estado == WAIT0) {
    estado_LCD1 = 0;
    
    // Verifica se uma tag é apresentada:
    if (RFID.available()) {
      LCDtxt(5); // Display: AGUARDE...
      uint8_t c = RFID.read();
      
      // Decodificação da tag:
      if (RDM6300.decode(c)) {
        String str_msg = "";
        String str_aux = "";
        for (int i = 0; i < 5; i++) {
          if (String(RFID_payload[i], HEX).length() == 1) {
            str_aux = "0" + String(RFID_payload[i], HEX);
          } else {
            str_aux = String(RFID_payload[i], HEX);
          }
          str_msg = str_msg + str_aux;
        }

        // Horário:
        String horario = rtc_tempo("DD-MM-YYYY hh:mm:ss");

        // Verifica se a porta está fechada ou aberta:
        if (digitalRead(PINO_PORTA) == LOW) {
          porta = "P0";
        } else {
          porta = "P1";
        }

        // EXEMPLO DE ENVIO: 01000BCAE6/P0/10-10-2020 10:10:10

        // Formatação da mensagem:
        TX_tag_id = str_msg;
        str_msg.concat("/");
        str_msg.concat(porta);
        str_msg.concat("/");
        str_msg.concat(horario);
        str_msg.toUpperCase();
        
        // Conversão de string para char:
        str_msg.toCharArray(MQTT_payload, sizeof(MQTT_payload));

        // Verifica se o cliente está conectado e envia a mensagem de solicitação:
        while (!mqttClient.connected()) {
          mqtt_connexao();
        }
        while (!mqttClient.publish(MQTT_PUB, MQTT_payload)) {
          mqtt_connexao();
          mqttClient.publish(MQTT_PUB, MQTT_payload);
        }
        //Serial.print("Mensagem enviada: ");
        //Serial.println(MQTT_payload);
        
        estado = WAIT1;
        cont_espera = 0;
        temp_WAIT1 = millis();
      }
    }
  }

  // Verifica se o programa está a espera da resposta de uma solicitação:
  if (estado == WAIT1) {
    if (millis() - temp_WAIT1 > MQTT_WAIT1) {
      if (cont_espera == 0) {
        //Serial.println("Tente novamente!");
        cont_espera = 1;
        estado = WAIT0;
        LCDtxt (4); // Display: TENTE NOVAMENTE
        delay(1500);
        LCD.clear();
        discard();
      }
    }
  }
  mqttClient.loop();
}

// Função para exibição de texto no display LCD:
void LCDtxt (int x, String matricula) {
  estado_LCD0 = estado_LCD1;
  LCD.clear();
  LCD.setCursor(0, 0);
  switch (x) {
    case 0:
      estado_LCD1 = 0;
      LCD.print("INICIANDO O");
      LCD.setCursor(0, 1);
      LCD.print("DISPOSITIVO!");
      break;
    case 1:
      estado_LCD1 = 1;
      LCD.print("SALA M205");
      LCD.setCursor(0, 1);
      LCD.print("APRESENTE CARTAO");
      break;
    case 2:
      estado_LCD1 = 2;
      LCD.print("ACESSO LIBERADO");
      LCD.setCursor(0, 1);
      LCD.print(matricula);
      delay(2000);
      break;
    case 3:
      estado_LCD1 = 3;
      LCD.print("ACESSO NEGADO");
      delay(2000);
      break;
    case 4:
      estado_LCD1 = 4;
      LCD.print("TENTE NOVAMENTE");
      break;
    case 5:
      estado_LCD1 = 5;
      LCD.print("AGUARDE...");
      break;
  }
}

// Função para descartar dados seriais após a leitura de uma tag:
void discard() {
  while (RFID.available()) {
    uint8_t lixo = RFID.read();
  }
  memset(RFID_payload, 0, sizeof(RFID_payload));
}

// Rotina de interrupção do botao:
void interrupcao() {
  estado_porta0 = estado_porta;
  estado_porta = !estado_porta;
}

// Função para obter o horário do RTC:
String rtc_tempo(String rtc_str) {
  DateTime now = RTC.now();
  char format[20];
  rtc_str.toCharArray(format, 20);
  rtc_str = now.toString(format);
  return rtc_str;
}

// Função de nova conexão ao servidor MQTT:
void mqtt_connexao() {
  while (!mqttClient.connected()) {
    //Serial.println("Conectando ao servidor MQTT... ");
    if (mqttClient.connect(MQTT_CLIENT, MQTT_USER, MQTT_PWD)) {
      //Serial.println("Conectado!");
      mqttClient.subscribe(MQTT_SUB);
    } else {
      //Serial.print("Falha! Estado: ");
      //Serial.println(mqttClient.state());
      delay(1000);
    }
  }
}
