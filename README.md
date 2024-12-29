# OTA-ESP32-Upload-Code-PHP
Code mẫu để upload firmware ESP32 mọi nơi từ xa cho những sản phẩm đã release. 
## Cập nhật firmware từ xa
Cập nhật firmware OTA (Over the Air) là tiến trình tải firmware mới vào ESP module thay vì sử dụng cổng Serial. Tính năng này thực sự rất hữu dụng trong nhiều trường hợp giới hạn về kết nối vật lý đến ESP Module.

Sử dụng OTA với tùy chọn dùng Arduino IDE trong quá trình phát triển, thử nghiệm, 2 tùy chọn còn lại phù hợp cho việc triển khai ứng dụng thực tế, cung cấp tính năng cập nhật OTA thông qua web hay sử dụng HTTP Server.

Trong tất cả các trường hợp, thì Firmware hỗ trợ OTA phải được nạp lần đầu tiên qua cổng Serial, nếu mọi thứ hoạt động trơn tru, logic ứng dụng OTA hoạt động đúng thì có thể thực hiện việc cập nhật firmware thông qua OTA.

### Bảo mật

Khi ESP8266/ESP32 được phép thực thi OTA, có nghĩa nó được kết nối mạng không dây và có khả năng được cập nhập Sketch mới. Cho nên khả năng ESP bị tấn công sẽ nhiều hơn và bị nạp bởi mã thực thi khác là rất cao. Để giảm khả năng bị tấn công cần xem xét bảo vệ cập nhật của bạn với một mật khẩu, cổng sử dụng cố định khác biệt, v.v…

## PHP OTA Server

Bình thường sản phẩm Release ra thị trường sẽ được upload code sẵn và chứa firmware bao gồm chờ bản cập nhật mới và nó được kết nối với một domain, tuy nhiên ở mình sẽ demo cho bạn ở domain dùng ip động cho các bạn test code cho tiện. Để lấy được ip động của PC bạn vào cmd và gõ lệnh:
```bash
ipconfig
```

Kết quả như sau:
![images](https://github.com/VinhCao09/OTA-ESP32-Upload-Code-PHP/blob/main/img/1.jpg)

Copy IP động!

Thay IP động trong source code Server nhé! 

![images](https://github.com/VinhCao09/OTA-ESP32-Upload-Code-PHP/blob/main/img/2.jpg)

Đổi tên folder chứa  code server, ở đây của mình xài xampp nên có địa chỉ như sau:

![images](https://github.com/VinhCao09/OTA-ESP32-Upload-Code-PHP/blob/main/img/3.jpg)


![images](https://github.com/VinhCao09/OTA-ESP32-Upload-Code-PHP/blob/main/img/4.jpg)


## 🚀 About Me
Hello 👋I am Vinh. I'm studying HCMC University of Technology and Education

**Major:** Electronics and Telecommunication

**Skill:** 

*- Microcontroller:* ESP32/8266 - ARDUINO - PIC - Raspberry Pi

*- Programming languages:* C/C++/HTML/CSS/PHP/SQL and
related Frameworks (Bootstrap)

*- Communication Protocols:* SPI, I2C, UART, CAN

*- Data Trasmissions:* HTTP, TCP/IP, MQTT
## Authors

- [@my_fb](https://www.facebook.com/vcao.vn)
- [@my_email](contact@vinhcaodatabase.com)

## Demo

![Logo](https://codingninja.asia/images/codeninjalogo.png)



