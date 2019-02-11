<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();


/*************************************************************************
 * Parse data
 *************************************************************************/

$arBlogs = $arParams["BLOG_POST"];

if ( is_array( $arBlogs ) )
{
    foreach ( $arBlogs as $arBlog )
    {
        if ( is_array( $arBlog ) )
        {
            $arPosts = $arBlog["POSTS"];
            if ( is_array( $arPosts ) )
            {
                foreach ( $arPosts as $arPost )
                {
                    if ( $arPost["DATE_PUBLISH"] )
                    {
                        $arDateTime = explode(
                            ' ' ,
                            $arPost["DATE_PUBLISH"]
                        );
                    }
                    $arPost["DATE_PUBLISH"] = trim( $arDateTime[0] );
                    $arPost["TIME_PUBLISH"] = trim( $arDateTime[1] );

                    if ( $arPost["DATE_CREATE"] )
                    {
                        $arDateTime = explode(
                            ' ' ,
                            $arPost["DATE_CREATE"]
                        );
                    }
                    $arPost["DATE_CREATE"] = trim( $arDateTime[0] );
                    $arPost["TIME_CREATE"] = trim( $arDateTime[1] );

                    $arPost["URL"] = CBlogPost::PreparePath(
                        $arBlog["BLOG_URL"] ,
                        $arPost["ID"] ,
                        SITE_ID
                    );
                    $arResult["BLOG_POST"][] = $arPost;

                    //echo '<pre>'; print_r( $arPost ); echo '</pre>';
                }
            }
        }
    }
}

$this->IncludeComponentTemplate();


