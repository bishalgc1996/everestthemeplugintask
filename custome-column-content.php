<?php defined( 'ABSPATH' ) or die(); ?>
    <textarea class="shortcode_display" style="resize: none;" rows="3" cols="50"
              readonly="readonly">
<?php //var_dump($a);?>
    [map_shortcode id=<?php echo $id ?> lat =<?php echo $a['0']['latitude'] ?> log =<?php echo $a[0]['longitute'] ?>]
</textarea>
