this application contains an api for online selling medicine portal

it contains following sections

Medication category
--------------------------------------------------
each medication containes madication category 


Medication
------------------------------------------------
medications are the product which is going to sell


Lead
--------------------------------------------------
admin will contact users to collect a requirement which called lead once he subscribed the lead he will become subscriber
-lead contains address and customer info 


Order
------------------------------------------------
-subscribed user will place and order 
-admin can download pdf too which is uploaded on AWS S3


This code contains following laravel functionality
----------------------------------------------------
-use passport for api authentication 
which will maintiain token based authentication
-used pagination to retrieve data
-eloquent relationship 
for .e.g 
orders can have multiple order item https://prnt.sc/txqj6w
orders can have shipping address
-pdf generation 
for .e.g 
https://prnt.sc/txqk1r
-upload file on aws 
https://prnt.sc/txqk1r


How to check an api
-------------------------------------------------------------
1.create user user using register api https://prnt.sc/txqp21
2.Do login using those crdentials https://prnt.sc/txqpug
3.use key to fetch data https://prnt.sc/txqqkp https://prnt.sc/txqr03
