package com.example.jured.superstore2.products;

import android.os.AsyncTask;
import android.util.Log;

import com.example.jured.superstore2.MainActivity;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by jured on 17/01/16.
 */
public class ProductAsyncTask extends AsyncTask<String, Void, List<Product>> {

    public final String PRODUCTS_URL = "http://10.10.20.179/be/products/all";
    MainActivity mainActivity;


    public ProductAsyncTask(MainActivity mainActivity) {
        this.mainActivity = mainActivity;
    }

    @Override
    protected List<Product> doInBackground(String... params) {

        String data = "";
        InputStream is = null;
        HttpURLConnection connection;

        try {
            URL url = new URL(PRODUCTS_URL);
            connection = (HttpURLConnection) url.openConnection();
            connection.connect();

            is = connection.getInputStream();

            BufferedReader br = new BufferedReader(new InputStreamReader(is));
            StringBuffer sb = new StringBuffer();

            String line = "";
            while ((line = br.readLine()) != null) {
                sb.append(line);
            }

            data = sb.toString();

            Log.d("DEBUG", data);

            JSONArray json = new JSONArray(data);

            List<Product> products = new ArrayList<>();
            for (int i = 0; i < json.length(); i++) {
                JSONObject o = json.getJSONObject(i);
                products.add(new Product(
                        o.getInt("product_id"),
                        o.getString("name"),
                        o.getString("description"),
                        o.getDouble("price"),
                        o.getInt("active")
                ));

            }

            return products;

        } catch (IOException | JSONException e) {
            e.printStackTrace();
        }


        return null;
    }


    @Override
    protected void onPostExecute(List<Product> products) {
        mainActivity.setProductsAndRefreshDisplay(products);
    }
}
