/*! @preserve
 * Plugin Name:       Woo Align Buttons
 * Plugin URI:        https://wordpress.org/plugins/woo-align-buttons
 * Description:       A lightweight plugin to align WooCommerce "Add to cart" buttons.
 * Version:           3.6.8
 * Author:            320up
 * Author URI:        https://320up.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
// Pure javascript version
// Activate script in class-woo-align-public.php
var wooAlignButtons = function(title, wrapper) {
    "use strict";
    var parent = document.querySelectorAll("ul.products");
    if (parent.length) {
        parent.forEach(function(value, index, array) {
            var gridRows = [];
            var tempRow = [];
            var children = Array.from(value.children);
            if (children.length) {
                children.forEach(function(child, index, array) {
                    var compStyles = window.getComputedStyle(child);
                    var clear = compStyles.getPropertyValue("clear");
                    if (clear !== "none" && index !== 0) {
                        gridRows.push(tempRow);
                        tempRow = [];
                    }
                    tempRow.push(child);
                    if (children.length === index + 1) {
                        gridRows.push(tempRow);
                    }
                    if (gridRows.length) {
                        gridRows.forEach(function(row, index, array) {
                            function sameHeight(target, space) {
                                var heights = [];
                                row.forEach(function(item, index, array) {
                                    var elem = item.querySelector(target);
                                    if (elem === null) {
                                        // null
                                    } else {
                                        elem.style.minHeight = "0px";
                                        elem.style.paddingBottom = "";
                                        var height = elem.clientHeight;
                                        heights.push(height);
                                    }
                                });
                                var high = Math.max.apply(null, heights);
                                row.forEach(function(item, index, array) {
                                    var elem = item.querySelector(target);
                                    if (elem === null) {
                                        // null
                                    } else {
                                        elem.style.minHeight = high + space + "px";
                                        if (window.matchMedia("(max-width: 320px)").matches) {
                                            elem.style.minHeight = "0px";
                                            elem.style.paddingBottom = "";
                                        }
                                    }
                                });
                            }
                            sameHeight(title, 1);
                            sameHeight(wrapper, 10);
                        });
                    }
                });
            }
        });
    }
};
window.onload = function() {
    // Must have two entries.
    /* Content of wooAlignButtons(title, wrapper); */
    wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
};
window.addEventListener("resize", function() {
    setTimeout(function() {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 250);
});
window.addEventListener("load", function() {
    setTimeout(function() {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 2000);
    setTimeout(function() {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 5000);
    setTimeout(function() {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 7000);
});
