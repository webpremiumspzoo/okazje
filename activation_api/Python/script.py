import json
import md5
import pycurl
try:
    # python 3
    from urllib.parse import urlencode
except ImportError:
    # python 2
    from urllib import urlencode

url = "https://okazje.webpremium.pl/crontab/activateapi"; 

client_id = "xxxxxxxxxxxxxxxxxxxx"
client_secret = "xxxxxxxxxxxxxxxxxxxx"
promotion_id = "3000000000"
action = "activate"

jsonParams = '{"client_id":"' + client_id +'","client_secret":"' + client_secret + '","promotion_id":"' + promotion_id + '","action":"' + action + '"}'
checksum = md5.new(jsonParams).hexdigest()

postdata = {'client_id': client_id,'client_secret': client_secret, 'promotion_id': promotion_id,'action': action,'checksum' : checksum}
postfields = urlencode(postdata)

c = pycurl.Curl()
c.setopt(c.URL, url)
c.setopt(c.POSTFIELDS, postfields)
c.perform()
c.close()
