(window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[]).push([[14],{542:function(o,c,t){"use strict";t.r(c);var n=t(8),e=t.n(n),r=t(0),s=t(1),a=(t(2),t(7)),i=t.n(a),u=t(45),k=t(73),b=(t(561),function(o){return Object(s.sprintf)(Object(s.__)("%d left in stock",'woocommerce'),o)}),d=function(o,c){return c?Object(s.__)("Available on backorder",'woocommerce'):o?Object(s.__)("In Stock",'woocommerce'):Object(s.__)("Out of Stock",'woocommerce')};c.default=Object(k.withProductDataContext)((function(o){var c,t=o.className,n=Object(u.useInnerBlockLayoutContext)().parentClassName,s=Object(u.useProductDataContext)().product;if(!s.id||!s.is_purchasable)return null;var a=!!s.is_in_stock,k=s.low_stock_remaining,l=s.is_on_backorder;return Object(r.createElement)("div",{className:i()(t,"wc-block-components-product-stock-indicator",(c={},e()(c,"".concat(n,"__stock-indicator"),n),e()(c,"wc-block-components-product-stock-indicator--in-stock",a),e()(c,"wc-block-components-product-stock-indicator--out-of-stock",!a),e()(c,"wc-block-components-product-stock-indicator--low-stock",!!k),e()(c,"wc-block-components-product-stock-indicator--available-on-backorder",!!l),c))},k?b(k):d(a,l))}))},561:function(o,c){}}]);