
[![Build Status](https://travis-ci.com/Expirio/Expirio_Backend.svg?branch=develop)](https://travis-ci.com/Expirio/Expirio_Backend)
[![License](https://poser.pugx.org/apinstein/expiring-hash/license)](https://packagist.org/packages/apinstein/expiring-hash)

![Build history](https://buildstats.info/travisci/chart/Expirio/Expirio_Backend?branch=develop)

## Todo

- [ ] Write E2E tests to check also how we handles edge cases and error responses

## Setup and Run
```
composer install
php bin/console server:stop
redis-server
=> http://127.0.0.1:8000/ping
```



----
# Create slot page
http://127.0.0.1:8000/create

# Create slot Action
| CLI | HTTP(GET) |
|-- |--- |
| bin/console pair:create --password=sesame1 --expire_in=PT200S | http://127.0.0.1:8000/create/sesame1/P5D |
| Read slot: 6bdc77c4-8a6e-4358-b403-114ce4b8ae20, Write slot: 65fca850-277d-4d74-b875-f9629c8f3215 | {read_url:	"/6bdc77c4-8a6e-4358-b403-114ce4b8ae20", write_url:	"/65fca850-277d-4d74-b875-f9629c8f3215"} |


### Redis state:
<table>
  <thead>
    <td>key</td>
    <td>Value</td>
  </thead>

  <tr>
    <td>6bdc77c4-8a6e-4358-b403-114ce4b8ae20</td>
    <td>
       
     {
       "password" : "sesame1",
       "attempts" : "0",
       "secret" : ""
     }
        
   </td>
  </tr>
  <tr>
    <td>65fca850-277d-4d74-b875-f9629c8f3215</td>
    <td>
       
      {
         "read_slot" : "6bdc77c4-8a6e-4358-b403-114ce4b8ae20"
      }
        
   </td>
  </tr>
 </table>

--- 
# Write slot page(GET)

127.0.0.1:8000/write/65fca850-277d-4d74-b875-f9629c8f3215

# Write slot action

| CLI | HTTP(PUT) |
|-- |--- |
| bin/console pair:write --writeuid=65fca850-277d-4d74-b875-f9629c8f3215 --secret="this is my secret" | 127.0.0.1:8000/write/65fca850-277d-4d74-b875-f9629c8f3215 |
| OK | 200 |  

### Redis state 

<table>
  <thead>
    <td>key</td>
    <td>Value</td>
  </thead>

  <tr>
    <td>6bdc77c4-8a6e-4358-b403-114ce4b8ae20</td>
    <td>
       
      {
         "password" : "3b438e86837ef71f01958024ac971aeae725cf70",
         "attempts" : "0",
         "secret" : "7ztz112zfSuUGY59+87vZNpQVC1OPN5Dm5r60dx6AME="
      }
        
   </td>
  </tr>
 </table>

---
# Read slot page(display form for password)
http://127.0.0.1:8000/read/6bdc77c4-8a6e-4358-b403-114ce4b8ae20

# Read slot action

| CLI | HTTP(GET) | 
|--- |--- |
| bin/console pair:read --readuid=6bdc77c4-8a6e-4358-b403-114ce4b8ae20 --password='sesame1' | http://127.0.0.1:8000/read/6bdc77c4-8a6e-4358-b403-114ce4b8ae20/sesame1 |
| This is my secret | This is my secret  |


### Redis state

<table>
  <thead>
    <td>key</td>
    <td>Value</td>
  </thead>
 </table>
