<?php
require_once dirname(__FILE__) . '/../../../../wp-load.php';

$recipes = get_transient( 'nook_phone_nh_recipes' );
echo "Recipes Count: " . (is_array($recipes) ? count($recipes) : 0) . "\n";

if (is_array($recipes) && count($recipes) > 0) {
    echo "First Recipe: \n";
    print_r($recipes[0]);
    $mats = json_decode( html_entity_decode( $recipes[0]['materials'] ), true );
    echo "Decoded Mats: \n";
    print_r($mats);
    
    // How many materials found total?
    $material_names = array();
    foreach ( $recipes as $r ) {
        if ( ! empty( $r['materials'] ) ) {
            $m = json_decode( html_entity_decode( $r['materials'], ENT_QUOTES, 'UTF-8' ), true );
            if ( is_array( $m ) ) {
                foreach ( array_keys( $m ) as $m_name ) {
                    $material_names[ $m_name ] = true;
                }
            }
        }
    }
    echo "Unique Materials: " . count($material_names) . "\n";
    print_r(array_keys($material_names));
}
