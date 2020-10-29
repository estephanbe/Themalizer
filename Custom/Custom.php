<?php
namespace BoshDev\Custom;


/**
 * Custom Functions Class
 */
class Custom
{
    
    function bod_rnta ($str) {

      $str = html_entity_decode($str);

      $western_arabic_nums = array('0','1','2','3','4','5','6','7','8','9');
      $eastern_arabic_nums = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

      $western_arabic_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
      $eastern_arabic_months  = array(
        "يناير",
        "فبراير",
        "مارس",
        "أبريل",
        "مايو",
        "يونيو",
        "يوليو",
        "أغسطس",
        "سبتمبر",
        "أكتوبر",
        "نوفمبر",
        "ديسمبر"
    );

      // use preg_replace instead of str_replace
      $str = str_replace($western_arabic_months, $eastern_arabic_months, $str);
      $str = str_replace($western_arabic_nums, $eastern_arabic_nums, $str);


      // fix replacing numbers in tags
      $str = preg_replace_callback("/\<.*?\>/", function($match){
        $match = preg_replace_callback('/[\x{0660}-\x{0669}]/u', function($num){
          $western_arabic_nums = array('0','1','2','3','4','5','6','7','8','9');
          $eastern_arabic_nums = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

          $indexStg = array_search($num[0], $eastern_arabic_nums);

          return $western_arabic_nums[$indexStg];
        }, $match);
        return $match[0];
      }, $str);
      
     


      return $str;
    }


    /*=============================================
                    BREADCRUMBS
    =============================================*/
    function the_breadcrumb()
    {
        $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
        $delimiter = '&raquo;'; // delimiter between crumbs
        $home = 'الرئيسية'; // text for the 'Home' link
        $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
        $before = '<span class="current">'; // tag before the current crumb
        $after = '</span>'; // tag after the current crumb

        global $post;
        $homeLink = get_bloginfo('url');
        if (is_home() || is_front_page()) {
            if ($showOnHome == 1) {
                echo '<div id="bod_crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';
            }
        } else {
            echo '<div id="bod_crumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
            if (is_category()) {
                $thisCat = get_category(get_query_var('cat'), false);
                if ($thisCat->parent != 0) {
                    echo get_category_parents($thisCat->parent, true, ' ' . $delimiter . ' ');
                }
                echo $before . 'الأرشيف بحسب المواضيع "' . single_cat_title('', false) . '"' . $after;
            } elseif (is_search()) {
                echo $before . 'نتائج البحث لِ "' . get_search_query() . '"' . $after;
            } elseif (is_day()) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
                echo $before . get_the_time('d') . $after;
            } elseif (is_month()) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                echo $before . get_the_time('F') . $after;
            } elseif (is_year()) {
                echo $before . get_the_time('Y') . $after;
            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
                    if ($showCurrent == 1) {
                        echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
                    }
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    $cats = get_category_parents($cat, true, ' ' . $delimiter . ' ');
                    if ($showCurrent == 0) {
                        $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                    }
                    echo $cat->name !== 'Uncategorized' ? $cats : '';
                    if ($showCurrent == 1) {
                        echo $before . get_the_title() . $after;
                    }
                }
            } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
                $post_type = get_post_type_object(get_post_type());
                echo $before . $post_type->labels->singular_name . $after;
            } elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                $cat = $cat[0];
                echo get_category_parents($cat, true, ' ' . $delimiter . ' ');
                echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
                if ($showCurrent == 1) {
                    echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
                }
            } elseif (is_page() && !$post->post_parent) {
                if ($showCurrent == 1) {
                    echo $before . get_the_title() . $after;
                }
            } elseif (is_page() && $post->post_parent) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo $breadcrumbs[$i];
                    if ($i != count($breadcrumbs)-1) {
                        echo ' ' . $delimiter . ' ';
                    }
                }
                if ($showCurrent == 1) {
                    echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
                }
            } elseif (is_tag()) {
                echo $before . 'المقالات بحسب الكلمة المفتاحية  "' . single_tag_title('', false) . '"' . $after;
            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                echo $before . 'المقالات التي نُشرت بواسطة ' . $userdata->display_name . $after;
            } elseif (is_404()) {
                echo $before . 'Error 404' . $after;
            }
            if (get_query_var('paged')) {
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                    echo ' (';
                }
                echo __('Page') . ' ' . get_query_var('paged');
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                    echo ')';
                }
            }
            echo '</div>';
        }
    } // end the_breadcrumb()


}

