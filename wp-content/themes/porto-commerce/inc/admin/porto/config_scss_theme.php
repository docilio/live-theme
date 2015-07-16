// Porto Config Scss Theme File
// Created at <?php echo date("Y-m-d H:i:s") ?>

<?php
$b = porto_check_theme_options();
?>

//** Padding between columns. Gets divided in half for the left and right.
$grid-gutter-width:         <?php echo $b['grid-gutter-width'] ?>px !default;

// Large screen / wide screen
$container-large-desktop: (<?php echo $b['container-width'] ?>px) !default;
$screen-large: "(max-width: <?php echo $b['container-width'] + 29 ?>px)";

// Border radius
<?php if ($b['border-radius']) : ?>
    $border-xs:     1px;
    $border-small:  2px;
    $border-thin:   3px;
    $border-base:   4px;
    $border-normal: 5px;
    $border-medium: 6px;
    $border-thick:  7px;
    $border-strong: 8px;
    $border-large:  16px;
    $progress-border: 25px;
    $feature-icon-border: 35px;
    $searchform-border: 20px;
    $searchform-border-large: 25px;

@mixin searchform-select-padding() {
    @if $rtl == 1 {
        padding: 0 10px 0 0;
    } @else {
        padding: 0 0 0 10px;
    }
}

@mixin searchform-input-padding() {
    @if $rtl == 1 {
        padding: 0 20px 0 15px;
    } @else {
        padding: 0 15px 0 20px;
    }
}

@mixin searchform-button-padding() {
    @if $rtl == 1 {
        padding: 0 13px 0 20px;
    } @else {
        padding: 0 20px 0 13px;
    }
}
    $searchform-extra-width: 0px;
    $thumb-link-border: 25px;
<?php else : ?>
    $border-xs:     0;
    $border-small:  0;
    $border-thin:   0;
    $border-base:   0;
    $border-normal: 0;
    $border-medium: 0;
    $border-thick:  0;
    $border-strong: 0;
    $border-large:  0;
    $progress-border: 0;
    $feature-icon-border: 0;
    $searchform-border: 0;
    $searchform-border-large: 0;

@mixin searchform-select-padding() {
    @if $rtl == 1 {
        padding: 0 5px 0 0;
    } @else {
        padding: 0 0 0 5px;
    }
}

@mixin searchform-input-padding() {
    padding: 0 10px 0 10px;
}

@mixin searchform-button-padding() {
    @if $rtl == 1 {
        padding: 0 10px 0 11px;
    } @else {
        padding: 0 11px 0 10px;
    }
}
    $searchform-extra-width: 12px;
    $thumb-link-border: 0;
<?php endif; ?>

<?php if ($b['thumb-padding']) : ?>
    //** Padding around the thumbnail image
    $thumbnail-padding:      4px !default;
    //** Thumbnail border color
    $thumbnail-border:       #ddd !default;
    $thumbnail-border-width: 1px;
    $thumbnail-slide-width:  99.5%;
    $product-image-padding:  0.2381em;
    $product-image-border-width: 1px;
    $widget-product-image-padding: 2px;
    $image-slider-padding: 3px;
<?php else : ?>
    //** Padding around the thumbnail image
    $thumbnail-padding:      0 !default;
    //** Thumbnail border color
    $thumbnail-border:       transparent !default;
    $thumbnail-border-width: 0;
    $thumbnail-slide-width:  100%;
    $product-image-padding:  0;
    $product-image-border-width: 0;
    $widget-product-image-padding: 0;
    $image-slider-padding: 0;
<?php endif; ?>