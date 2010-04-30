<?php
/**
 * Feed_item.
 *  This view is used in the taggedfeeds view and the main view.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * Public License (LGPL)
 */
 
 
?>

			<table class="table-list" id='feed_items_table'>
										<!--<thead>
											<tr>
							<th scope="col"><?php echo Kohana::lang('ui_main.title'); ?></th>
							<th scope="col"><?php echo Kohana::lang('ui_main.source'); ?></th>
							<th scope="col"><?php echo Kohana::lang('ui_main.date'); ?></th>
											</tr>
										</thead> -->
				<tbody>
                                    <?php foreach ($feeds as $feed) : ?>
                                        <?php
                                            $feed_id = $feed->id;
                                            $feed_title = text::limit_chars($feed->item_title, 40, '...', True);
                                            $feed_link = $feed->item_link;
                                            $feed_date = date('M j Y h:m', strtotime($feed->item_date));
                                        ?>
                                        <tr id="feed_row_<?php echo $feed_id ;?>" >
                                            <td>
                                                <div id="item_panel">
                                                    <?php if(isset($_SESSION['auth_user'])) : ?>
                                                        <a id="feed_link_<?php echo $feed_id ;?>" href="<?php echo(url::base()); ?>contentcuration/markasaccurate/<?php echo $feed_id ;?>/swiftriver_apala_markerId" title="Mark as Accurate" >
                                                    <?php endif; ?>
                                                            <div style="padding:5px;width:35px;height:45px;border:1px solid #660000;Text-align:center; -moz-border-radius: 5px; -webkit-border-radius: 5px;">
                                                                <img id="submit_feed_img<?php echo $feed_id ;?>" src="<?php echo url::base(); ?>/media/img/rssdark.png" alt="<?php echo $feed_title ?>" align="absmiddle" style="border:0" />
                                                                <br/>
                                                                <span style="font-weight:bold;color:#660033">
                                                                    <label id="weight_<?php echo $feed_id; ?>" name="weight_<?php echo $feed_id; ?>" >
                                                                        <?php if ($feed->weight == 0.00 || $feed->weight == -1 ){ echo "_" ;}else{ echo round($feed->weight,0 )."%"; } ?>
                                                                    </label>
                                                                </span>
                                                            </div>
                                                    <?php if(isset($_SESSION['auth_user'])): ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                                <td style="border-bottom:2px solid #AAAAAA;" id="feed_row_<?php echo $feed_id ;?>" >
                                                <div class="description">
                                                    <?php echo $feed->item_description ;?>
                                                </div>
                                                <p>&nbsp;</p>
                                                <p>
                                                    <strong>Delivered by <span style="text-transform: lowercase;"><?php echo util::get_category_name($feed->category_id ); ?></span></strong> on <?php echo $feed->item_date; /*$testDate;*/ ?>&nbsp;&nbsp;&nbsp;
                                                    <strong>Source:</strong> <a href="<?php echo $feed->item_link; ?>" target="_blank" style="color:#000000;">	<?php echo $feed->item_source; ?></a>
                                                </p>
                                                <label id="lblreport_<?php echo $feed_id; ?>" name="lblreport_<?php echo $feed_id; ?>" >
                                                </label>
                                                <div id="sweeper">
                                                    <form id="formtag<?php echo $feed_id ;?>" name="formtag<?php echo $feed_id ;?>"  method="POST" action="/main/tagging/feed/" >
                                                    <?php if(isset($_SESSION['auth_user'])){ ?>
                                                    <a href="javascript:submit_tags('<?php echo $feed_id ;?>','<?php //echo $feed->tablename ;?>')" title="Add a tag to this feed" >
                                                    <img src="<?php echo url::base(); ?>/media/img/tagbtn.png" alt="<?php echo $feed_title ?>" align="absmiddle" style="border:0" />
                                                    </a>
                                                    <input type=text id="tag_<?php echo $feed_id; ?>"  name="tag_<?php echo $feed_id; ?>" value="" />&nbsp;&nbsp;
                                                    <?php }else{ ?>
                                                    <img src="<?php echo url::base(); ?>/media/img/tagbtn.png" alt="<?php echo $feed_title ?>" align="absmiddle" style="border:0" />
                                                    <?php } ?>
                                                    <label id="lbltags_<?php echo $feed_id; ?>" name="lbltags_<?php echo $feed_id; ?>" >
                                                    <?php foreach($feed->tags as $tag): ?>
                                                    <li><b><?php echo $tag->type; ?></b>: <?php echo $tag->text; ?></li>
                                                    <?php endforeach; ?>
                                                    </label>
                                                        <div style="float:right">
                                                            <a href="<?php echo $feed->item_link; ?>" target="_blank"  title="Item Detail, Read the Item" >
                                                                <img src="<?php echo url::base(); ?>/media/img/newspaper.png" alt="<?php echo $feed_title ?>" align="absmiddle" style="border:0" />
                                                            </a>
                                                            <?php if(isset($_SESSION['auth_user'])) : ?>
                                                                <a id="reduce_ratting_link_<?php echo $feed_id ;?>" href="<?php echo(url::base()); ?>contentcuration/markasinaccurate/<?php echo $feed_id ;?>/falsehood/swiftriver_apala_markerId"  title="Mark as Inaccurate, Falsehood or Biased" >
                                                                    <img src="<?php echo url::base(); ?>/media/img/x_btn.png" alt="<?php echo $feed_title ?>" align="absmiddle" style="border:0" width="18" />
                                                                </a>
                                                                <a id='irrelevant_link_<?php echo $feed_id ; ?>' href="<?php echo(url::base()); ?>contentcuration/markascrosstalk/<?php echo $feed_id ;?>/swiftriver_apala_markerId"   title="Mark as Crosstalk"  >
                                                                    <img src="<?php echo url::base(); ?>/media/img/qtnmark.jpg" alt="<?php echo $feed_title ?>" align="absmiddle" style="border:0" />
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
				</tbody>
			</table>
			

			