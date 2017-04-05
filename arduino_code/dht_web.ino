#include <Bridge.h>
#include <HttpClient.h>
#include <Console.h>
#include "DHT.h"

#define DHTPIN 4  
//Temperature/humidity sensor setup:
#define DHTTYPE DHT11 

// Initialize DHT sensor for normal 16mhz Arduino
DHT dht(DHTPIN, DHTTYPE);

//initialize the temperature and humidity values
float h = 0;
float t = 0;
String data;
String url = "http://192.168.8.199:81/sws/add.php";

void setup() {
  // Bridge takes about two seconds to start up
  // it can be helpful to use the on-board LED
  // as an indicator for when it has initialized
  pinMode(13, OUTPUT);
  digitalWrite(13, LOW);
  Bridge.begin();
  digitalWrite(13, HIGH);

  Console.begin();

  while (!Console); // wait for a serial connection

  data = "";
  Console.println("DHT TEST PROGRAM "); 
  Console.println(); 
  Console.println("Type,\tstatus,\tHumidity (%),\tTemperature (C)");
}

void loop() {
// Initialize the client library
HttpClient client;

// READ DATA 
h = dht.readHumidity();
// Read temperature as Celsius
t = dht.readTemperature();

data = "?temp1=" + String(t)+ "&hum1="+ String(h);
  // Make a HTTP request:
  
  client.get(url+data);

  Console.println("Sent "+String(t)+"-"+String(h));
  // if there are incoming bytes available
  // from the server, read them and print them:
  while (client.available()) {
    char c = client.read();
    Console.print(c);
  }
  Console.flush();

  delay(5000);
}
