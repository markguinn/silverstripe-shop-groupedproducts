Grouped Products Submodule
==========================
For Silverstripe Shop
---------------------

Creates a new product type (i.e. Product subclass) that allows other
products to be grouped under it.

NOTE: This module is not functional yet. Should be fully working within
a few weeks.


Requirements
------------
- Silverstripe 3.1+ (may work with 3.0, but hasn't been tested)
- Shop Module 1.0 branch


Features
--------
- Provides new GroupedProduct class
- Backend functionality to easily create and manage grouped products
- Base templates for display of grouped products
	- Displays all child products, each with a quantity field
	- There is one "Add to Cart" button for all child products
- Child products can be hidden from display in searches and category
  listings via the ShowInMenu and ShowInSearch fields.
- Child products can (optionally) have their own detail page or not.
  Default is to redirect the detail pages back to the main grouped
  product. See RedirectChildProductsExtension.
- The base GroupedProduct class uses the standard SiteTree hierarchy,
  so child products are literally child pages of the GroupedProduct.
  The class is set up, though, so that someone could write a ManyManyGroupedProduct
  or we could add a config setting or something to allow a product
  to be in many GroupedProducts.
- The GroupedProduct itself may be purchasable or not.

Installation
------------
1. `composer require markguinn/silverstripe-shop-groupedproducts dev-master`


TODO
----
- EVERYTHING


Developer(s)
------------
- Mark Guinn <mark@adaircreative.com>

Contributions welcome by pull request and/or bug report.
Please follow Silverstripe code standards.


License (MIT)
-------------
Copyright (c) 2013 Mark Guinn

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is furnished to do so, subject
to the following conditions:

The above copyright notice and this permission notice shall be included in all copies
or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.