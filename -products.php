<?
// $products_profil =array();
$args = array(
    'numberposts' => -1,
    
    'category' => 36,
    'category__not_in' => 25 ,
    'orderby'   => 'title',
    'order'    => 'ASC', // ASC или DESC
    'suppress_filters' => true,
//    'post__in'  => $products_profil,
    'meta_query' => array(
        // array(
        // 	'key' => 'osnovnoj_razmer',
        // 	'value' => ' х 2000',
        //     'compare' => 'LIKE'
        // ),
        // array(
        // 	'key' => 'jalousie_list_price',
        // 	'value' =>18000,
        // 	'type' => 'numeric',
        // 	'compare' => '>='
        // )
    )
);
// var_dump($args);
$product_list = get_posts($args);


foreach ($product_list as $product) {
    if (get_field('osnovnoj_razmer', $product->ID)) {
        $razmerArr[] = get_field('osnovnoj_razmer', $product->ID);
    }
    $namesArr[] = $product->post_title;
    $namesArrIds[$product->post_title][] = $product->ID;
    // var_dump($product->ID);
}
 



foreach (array_unique($razmerArr) as $razmer) {
    $razmerWidth[] = preg_replace("/\s+/", "", explode('х', $razmer)[0]);
    $razmerHeight[] = preg_replace("/\s+/", "", explode('х', $razmer)[1]);
}

$namesArr = array_unique($namesArr);
$razmerWidth = array_unique($razmerWidth);
$razmerHeight = array_unique($razmerHeight);

sort($razmerWidth);
sort($razmerHeight);
// sort($namesArr); //РАНЕЕ БЫЛО ТАК, ПРОСИЛИ ОТСОРТИРОВАТЬ ОПРЕДЕЛЕННЫМ ОБРАЗОМ
// var_dump(sort(array_unique($razmerHeight)));

// [0] => Blitz [1] => Brillant [2] => Delight Decor [3] => Delight Design [4] => Geneo [5] => Grazio [6] => Intellio 80 [7] => Thermo 
$namesArr = array('Blitz','Thermo','Grazio','Delight Design','Delight Decor','Brillant','Intellio 80','Geneo')


?>


<section id="other_sizes">

    <div class="container">

        <div class="products_filter_">
            <?

            echo '<form action="" method="POST" id="filter" >';
            echo '<input  type="hidden" name="page_num"  />';
            // минимальная/максимальная цена
            echo '<div class=select_params_list>';

            echo '<div>';
            echo '<input  type="text" name="cena_min" placeholder="От" />';
            echo '</div>';

            echo '<select name="products_width" >
            <option disabled>Не выбрано</option>
            <option value="Не выбрано" >Не выбрано</option>';
            // echo '<select name="products_width"><option disabled>Ширина</option>';
            foreach ($razmerWidth as $width) {
                echo '<option value="' . $width . '">' . $width . '</option>'; // 
            }
            echo '</select>';

            echo '<select name="products_height" >
                  <option disabled>Не выбрано</option>
                    <option value="Не выбрано" >Не выбрано</option>';
            // echo '<select name="products_height"><option disabled>Высота</option>';
            foreach ($razmerHeight as $height) {
                echo '<option value="' . $height . '">' . $height . '</option>'; // 
            }
            echo '</select>';

            echo '<select name="products_profil" >
                  <option disabled>Не выбрано</option>
            <option value="Не выбрано" >Не выбрано</option>';
            // echo '<select name="products_profil"><option disabled>Тип профиля</option>';
            // foreach ($namesArr as $name) {
            //     echo '<option value="' . $name . '">' . $name . '</option>'; // 
            // }
            foreach ($namesArr as $name) {
                echo '<option value="' . implode(',',$namesArrIds[$name]) . '">' . $name . '</option>'; // 
            }
            echo '</select>';

            echo '</div>';

            echo '<button>Применить фильтр</button><input type="hidden" name="action" value="myfilter" style="pointer-events:none">
                        </form>
              <!--   <div id="response">тут фактически можете вывести посты без фильтрации </div>-->';
            ?>
        </div>






        <div id="response"  class="products__list">
            <?
            // var_dump(get_query_var('paged'));
          
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $params = array(
                'numberposts' => -1,
                // 'orderby'   => 'title',
                'order'    => 'ASC', // ASC или DESC
                'meta_key' => 'jalousie_list_price',
                'orderby' => 'meta_value_num',
           
        
                'posts_per_page' => 12, // количество постов на странице
                // 'category' => 25,
                // 'post_type' => 'post'
               'category' => 36,
               'category__not_in' => 25 ,
                'post__in'  => [ ],
                'paged' => $paged, // страница пагинации
                'suppress_filters' => true,

            );
            // query_posts($params);
            global $wp_query;
            $save_wpq = $wp_query;
            $wp_query = new WP_Query($params);
            while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                <div class="other_size__tab_content__item" data-itemId="<?= get_the_title(); ?>">
                    <? if (get_field('form-faktor') != '') : ?>
                        <span class="form-factor"><?= get_field('form-faktor'); ?>
                            <? if (get_field('form-faktor') != 'Балконный блок') {
                                echo 'окно';
                            }
                            ?>
                        </span>
                    <?else:?>
                        <span class="form-factor">
                            <? if (get_field('product_form_factor') != '') {
                                echo get_field('product_form_factor');
                            }
                            ?>
                        </span>
                    <? endif; ?>
                    <? if (get_field('form-faktor') == 'Одностворчатое') {
                        $margin = 'style="margin-left:auto;margin-right:auto;"';
                    } else {
                        $margin = '';
                    }
                    ?>
                    <a <?= $margin; ?> href="<?php echo the_permalink(); ?>" class="other_size__tab_content__item-img">

                        <? if (get_field('product_window_img')) : ?>

                            <img 
                                <?//if (get_field('product_window_img')=='https://www.oknastars.ru/wp-content/uploads/1_window.png') echo 'width="149"';?>
                                <?if (get_field('product_window_img')=='https://www.oknastars.ru/wp-content/uploads/1_window.png') echo 'width="116"';?>
                                <?if (get_field('product_window_img')=='https://www.oknastars.ru/wp-content/uploads/window-price-2.jpg') echo 'width="272"';?>
                                <?if (get_field('product_window_img')=='https://www.oknastars.ru/wp-content/uploads/window-price-1.jpg') echo 'width="186"';?>
                                decoding="async" 
                                loading="lazy" 
                                src="<?= get_field('product_window_img');?>" 
                                alt="<?= get_the_title(); ?>" 
                                title=" REHAU <? $strings = preg_split('/ /', get_the_title()); echo implode(' ', array_slice($strings, 0, 2)); ?>"
                            >
                        <? endif; ?>
                    </a>
                    <a href="<?php echo the_permalink(); ?>" class="other_size__tab_content__item-name">
                        <!-- <? the_title(); ?> -->
                        REHAU 
                        <?
                        $strings = preg_split('/ /', get_the_title());
                        echo implode(' ', array_slice($strings, 0, 2));
                        ?>
                    </a>
                    <div class="other_size__tab_content__item-size">
                        <? if (get_field('osnovnoj_razmer')) : ?>
                            <? echo str_replace('х', '<span>х</span>', get_field('osnovnoj_razmer')); ?>
                        <? endif; ?>
                    </div>
                    <div class="other_size__tab_content__item-price">
                        <? if (get_field('jalousie_list_price')) : ?>
                            от
                            <strong>
                                <?= number_format(get_field('jalousie_list_price'), 0, '.', ' ') ?>
                            </strong>

                            ₽
                        <? endif; ?>

                    </div>
                    <div class="size_tabs_item__content__product__href">
                        <a href="<?php echo the_permalink(); ?>" class="btn-link-text">
                            <span class="btn-link-text__title">Подробнее</span>
                            <span class="btn-link-text__icon"></span>
                        </a>
                    </div>



                </div>
            <? endwhile;
            // wp_pagenavi();
                $pagenum = (int) $_GET['page'];
                if ( !$pagenum){
                $pagenum = (get_query_var('paged')) ? get_query_var('paged') : 1;

                }
                $result =  paginate_links( array(
                    'base'         => @add_query_arg('page','%#%'),
                    'format'       => '%#%',
                    'current'   => $pagenum,
                    'add_args' => 'test',
                    'mid_size'  =>3,
                        'end_size' => 1,
                    'total'   => $wp_query->max_num_pages,
                    'prev_text' => '<',
                    'next_text' => '>'
                ) );
                $result = str_replace ('/wp-admin/admin-ajax.php', '', $result);
                echo ($result) ? "<div class='wp-pagenavi'>".$result."</div>" : '';
            ?>
            <script>
                document.querySelectorAll('a.page-numbers').forEach(element => {
                    element.addEventListener('click',function(){
                        this.preventDefault
                       return false
                    })
                });
            </script>

            <?php wp_reset_postdata();
            wp_reset_query();
            $wp_query = $save_wpq; ?>




        </div>
    </div>
</section>