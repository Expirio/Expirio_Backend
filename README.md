
## Todo

- [ ] Add routes framework
- [ ] Add read and write templates

----
# Create slot
| CLI | HTTP(GET) |
|-- |--- |
| bin/console pair:create --password=sesame1 --expire_in=PT200S | http://127.0.0.1:8000/create/mypassword/P5D |
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
# Write slot

| CLI | HTTP(PUT) |
|-- |--- |
| bin/console pair:write --writeuid=65fca850-277d-4d74-b875-f9629c8f3215 --secret="this is my secret" | 127.0.0.1:8000/write/65fca850-277d-4d74-b875-f9629c8f3215 |
| Secret stored | 200 response |  

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


## Cli: Read secret

> bin/console pair:read --readuid=6bdc77c4-8a6e-4358-b403-114ce4b8ae20 --password='sesame1'
>
>        This is my secret

<table>
  <thead>
    <td>key</td>
    <td>Value</td>
  </thead>
 </table>
