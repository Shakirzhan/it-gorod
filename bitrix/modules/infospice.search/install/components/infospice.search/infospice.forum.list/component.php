<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


/*************************************************************************
			Parse data 
*************************************************************************/

$arForums = $arParams[ "FORUM_TOPICS" ];

if ( is_array( $arForums ))
{
	foreach ( $arForums as $arForum ) 
	{	
		if ( is_array( $arForum ))
		{
			//echo '<pre>'; print_r( $arForum ); echo '</pre>';
			$arTopics = $arForum[ "TOPICS" ];
			if ( is_array( $arTopics ))
			{
				foreach ($arTopics as $arTopic ) 
				{
					//
					// Получение связку путей форумов с сайтом
					// ... взято отсюда: http://dev.1c-bitrix.ru/api_help/forum/developer/cforumnew/getsites.php
					// 
					$arForumPaths = CForumNew::GetSites( $arForum[ "ID" ]);
					$arForumPathsCodes = array_keys( $arForumPaths );
					for ( $i = 0; $i < count( $arForumPathsCodes ); $i++ )
					{
						$sid = $arForumPathsCodes[ $i ];

					    $arForumPaths[ $sid ] = CForumNew::PreparePath2Message(
				        	$arForumPaths[ $sid ], 
				        	array(
                           		"FORUM_ID"   => $arForum[ "ID"  ],
                                "TOPIC_ID"   => $arTopic[ "ID"  ],
                                "MESSAGE_ID" => $arForum[ "MID" ]
                            )
                        );
					}

					$arDateTime = explode( ' ', $arTopic[ "START_DATE" ]);
					$arTopic[ "START_DATE" ] = trim( $arDateTime[ 0 ]);
					$arTopic[ "START_TIME" ] = trim( $arDateTime[ 1 ]);

					$arTopic[ "URL" ] = $arForumPaths[ SITE_ID ]; // тут не понятно, что делать есть много сайтов
					$arResult[ "ITEMS" ][] = $arTopic;

					//echo '<pre>'; print_r( $arTopic ); echo '</pre>';
				}
			}				
		}
	}
}

$this->IncludeComponentTemplate();


