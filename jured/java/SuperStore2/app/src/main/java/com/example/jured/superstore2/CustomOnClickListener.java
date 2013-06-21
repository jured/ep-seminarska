package com.example.jured.superstore2;

import android.content.Intent;
import android.view.View;

import com.example.jured.superstore2.products.Product;

/**
 * Created by jured on 17/01/16.
 */
public class CustomOnClickListener implements View.OnClickListener {

    private MainActivity mainActivity;
    private Product product;

    public CustomOnClickListener(MainActivity mainActivity, Product product) {
        this.mainActivity = mainActivity;
        this.product = product;
    }

    @Override
    public void onClick(View v) {
        Intent intent = new Intent(mainActivity.getApplicationContext(), ProductDetailsActivity.class);
        intent.putExtra("text", product.toString());
        mainActivity.startActivity(intent);
    }
}
