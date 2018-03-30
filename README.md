# knapsack-example
Knapsack algorithm implementation based on https://stackoverflow.com/a/49353839/4448410

# How to select N products that their price sums to S randomly
This repo provides an example of an algorithm to :
 * select N products from a mysql database table
 * their prices sum to S
 * do this randomly(or appear random)
 
 # Knapsack problem
 This is a version of the https://en.wikipedia.org/wiki/Knapsack_problem
 
 # Algorithm
 The algorithm is based on the following:
 
 > In a collection of n positive numbers that sum up to S, at least one of them will be less than S divided by n (S/n)
 
 ## Steps
1.  Select a product randomly where price < S/N. Get its price, lets say X.
2.  Select a product randomly where price < (S - X)/(N-1). Get its price, assume Y.
3.  Select a product randomly where price < (S - X - Y)/(N-2).
4.  Repeat this and get till you get N-1 elements and the remainingPrive P.
5.  Select a product that price equals to P. If not found, repeat.

 ## Variation
 
 
 # Usage
 This is intended to be used for providing random collections of products that their price sum to a fixed value.
