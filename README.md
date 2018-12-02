
## Backend

- [ ] Add routes framework
- [ ] Add read and write templates


## System:

- [ ] Think about nginx
- [ ] How to provision a centos (needed vagrant to try)


## Deployiment

- [ ] Think about deploying

----

## Cli commands:


### Create slot:                                                                                                                                    

> bin/console pair:create --password=sesame1 --expire_in=PT5S
> 
>        Read slot: 6bdc77c4-8a6e-4358-b403-114ce4b8ae20
>
>        Write slot: 65fca850-277d-4d74-b875-f9629c8f3215

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

### Write secret:

> bin/console pair:write --writeuid=65fca850-277d-4d74-b875-f9629c8f3215 --secret="this is my secret"
>
>     Secret stored

<table>
  <thead>
    <td>key</td>
    <td>Value</td>
  </thead>

  <tr>
    <td>42b7c188-4500-414d-84b2-4a53fcd5d4f1</td>
    <td>
       
      {
         "password" : "3b438e86837ef71f01958024ac971aeae725cf70",
         "attempts" : "0",
         "secret" : "7ztz112zfSuUGY59+87vZNpQVC1OPN5Dm5r60dx6AME="
      }
        
   </td>
  </tr>
 </table>
