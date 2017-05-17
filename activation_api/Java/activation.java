import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.nio.charset.Charset;
 
import org.apache.commons.codec.binary.Base64;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.commons.codec.digest.DigestUtils ;

public class HttpPostReq
{
    public static void main(String args[])
    {
        String restUrl="https://okazje.webpremium.pl/crontab/activateapi";
        
        
        JSONObject rparams = new JSONObject();
        rparams.put("client_id", "WPROWADZ DANE");
        rparams.put("client_secret", "WPROWADZ DANE");
        rparams.put("promotion_id", "3000000000");
        rparams.put("action", "activate");
        
        String toHash = rparams.toString();
		String md5Hex = DigestUtils
			.md5Hex(toHash);
         
		rparams.put("checksum", md5hex);
        
        String jsonData=rparams.toString();
        HttpPostReq httpPostReq=new HttpPostReq();
        HttpPost httpPost=httpPostReq.createConnectivity(restUrl);
        httpPostReq.executeReq( jsonData, httpPost);
    }
     
    HttpPost createConnectivity(String restUrl)
    {
        HttpPost post = new HttpPost(restUrl);

        post.setHeader("Content-Type", "application/json");
            post.setHeader("Accept", "application/json");
            post.setHeader("X-Stream" , "true");
        return post;
    }
     
    void executeReq(String jsonData, HttpPost httpPost)
    {
        try{
            executeHttpRequest(jsonData, httpPost);
        }
        catch (UnsupportedEncodingException e){
            System.out.println("error while encoding api url : "+e);
        }
        catch (IOException e){
            System.out.println("ioException occured while sending http request : "+e);
        }
        catch(Exception e){
            System.out.println("exception occured while sending http request : "+e);
        }
        finally{
            httpPost.releaseConnection();
        }
    }
     
    void executeHttpRequest(String jsonData,  HttpPost httpPost)  throws UnsupportedEncodingException, IOException
    {
        HttpResponse response=null;
        String line = "";
        StringBuffer result = new StringBuffer();
        httpPost.setEntity(new StringEntity(jsonData));
        HttpClient client = HttpClientBuilder.create().build();
        response = client.execute(httpPost);
        System.out.println("Post parameters : " + jsonData );
        System.out.println("Response Code : " +response.getStatusLine().getStatusCode());
        BufferedReader reader = new BufferedReader(new InputStreamReader(response.getEntity().getContent()));
        while ((line = reader.readLine()) != null){ result.append(line); }
        System.out.println(result.toString());
    }
} 
