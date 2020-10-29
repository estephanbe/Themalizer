<?php
namespace BoshDev\Custom;



/**
 * Main Nav Walker
 */
class CommentsWalker extends \Walker_Comment
{

	function html5_comment( $comment, $depth, $args ) 
    {
        global $bod;
        $tag = 'li';

        $comment_author_url = get_comment_author_url( $comment );
        $comment_author     = get_comment_author( $comment );
        $avatar             = get_avatar( $comment, $args['avatar_size'] );


        $comment_timestamp = sprintf( __( '%1$s في %2$s', $bod->textDomain ), get_comment_date( '', $comment ), get_comment_time() );

        $comment_reply_link = get_comment_reply_link(
                        array_merge(
                            $args,
                            array(
                                'add_below' => 'div-comment',
                                'depth'     => $depth,
                                'max_depth' => $args['max_depth'],
                                'reply_text' => 'رد<i class="licon-reply"></i>',
                            )
                        )
                    );

        ?>

        <<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment' ); ?>>
        <article>

            <div class="gravatar">

                <?php

                if ( 0 !== $args['avatar_size'] ) {
                    if ( empty( $comment_author_url ) ) {
                        echo wp_kses_post( $avatar );
                    } else {
                        printf( '<a href="%s">', $comment_author_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped in https://developer.wordpress.org/reference/functions/get_comment_author_url/
                        echo wp_kses_post( $avatar );
                    }
                }

                if ( ! empty( $comment_author_url ) ) {
                    echo '</a>';
                }

                ?>

                
            </div>

            <div class="comment-body">

                <header class="comment-meta">

                    <h6 class="comment-author">
                        <?php

                        if ( !empty( $comment_author_url ) ) {
                           
                            printf( '<a href="%s">', $comment_author_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped in https://developer.wordpress.org/reference/functions/get_comment_author_url/
                        }

                        printf(
                            '%1$s',
                            esc_html( $comment_author )
                        );

                        if ( ! empty( $comment_author_url ) ) {
                            echo '</a>';
                        }

                        ?>

                    </h6>

                    

                    <div class="comment-info">
                        <time datetime="<?php comment_time( 'c' ); ?>" class="comment-date"><?php echo esc_attr( $comment_timestamp ); ?></time>

                        <?php

                        if ( $comment_reply_link ) {
                            echo $comment_reply_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Link is escaped in https://developer.wordpress.org/reference/functions/get_comment_reply_link/
                        }

                        ?>

                        <!-- <a href="#" class="comment-reply-link"><i class="licon-reply"></i>Reply</a> -->
                    </div>

                </header>

                <p><?php comment_text(); ?></p>

            </div><!--/ .comment-body-->



        </article>






    
    <?php 
    echo "</$tag>";
    }
}


