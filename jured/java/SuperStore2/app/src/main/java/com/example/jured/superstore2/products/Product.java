package com.example.jured.superstore2.products;

/**
 * Created by jured on 17/01/16.
 */
public class Product {

    private int product_id;
    private String name;
    private String description;
    private Double price;
    private int active;

    public Product(int product_id, String name, String description, Double price, int active) {
        this.product_id = product_id;
        this.name = name;
        this.description = description;
        this.price = price;
        this.active = active;
    }

    public int getProduct_id() {
        return product_id;
    }

    public String getName() {
        return name;
    }

    public String getDescription() {
        return description;
    }

    public Double getPrice() {
        return price;
    }

    public boolean isActive() {
        return active == 0;
    }

    @Override
    public String toString() {
        return "Product{" +
                "product_id=" + product_id +
                ", name='" + name + '\'' +
                ", description='" + description + '\'' +
                ", price=" + price +
                ", active=" + active +
                '}';
    }
}
