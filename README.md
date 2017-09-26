# xdt
XDT at first glance, aims to behave like jQuery on server side. We all know that there already so  many powerful tools that can traverse easly XML DOM such as simpleXML, XPath and so on. But sometimes,  we need something that is familiar, intitive; not only because it can do more than existing ones,  but because it remind us things we know already. XDT wanted to perpetrate jQuery moto "Write less, do more" which consists of writing few lines of code and doing it very well. In order to do this, XDT imitates jQuery selection technique, and implements the same methods names as jQuery and do almost the same thing.

XDT traverses XML DOM; It implements various methods to facilidate elements selection; such as getElementById, 
getElementsByClass, getElementsByAttr, getElementsByPseudo; the selector used for elements selection respects 
CSS structure, it also defines jQuery special selectors, such as nth, first, last, eq, and not.

XDT is developped into two differents versions: a version that support namespaces and a version that do not. XDT has no dependencies, It support PHP version 5.3 and earlier.
