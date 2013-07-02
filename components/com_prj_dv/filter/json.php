<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2010-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010-2012 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

function filter($res, $dd)
{
	global $com_name, $html_path;

	$data = $res['data'];
	$total = $res['total'];
	$found = $res['found'];

	$single_record = isset($dd['single'])? $dd['single']: false;

	$table = array();
	$table['__sql'] = $res['sql'];
	$table['sColumns'] = array();
	$table['aaData'] = array();
	$table['iTotalRecords'] = $total;
	$table['iTotalDisplayRecords'] = $found;
	$table['filters'] = isset($dd['filters'])? $dd['filters']: array();
	$table['cols'] = array();
	$vis_keys = array();
	$field_types = array();

	$header = isset($dd['cols'])? $dd['cols']: $data[0];
	$field_offset = 0;
	$first_col = true;

	foreach ($header as $key => $val) {
		if (count($data)>0) {
			$field_type = mysql_field_type($data, $field_offset);
		} else {
			$field_type = 'string';
		}

		$table['cols']['all'][] = $key;
		$field_offset++;

		if (!isset($dd['cols'][$key]['hide'])) {
			$table['cols']['visible'][] = $key;
			$vis_keys[] = $key;
			$align = (isset($dd['cols'][$key]['align']))? $dd['cols'][$key]['align']: false;
			$data_type = (isset($dd['cols'][$key]['data_type']))? $dd['cols'][$key]['data_type']: false;
			$data_type = (!$data_type)? $field_type: $data_type;

			switch($data_type) {
				case 'int':
				case 'float':
				case 'number':
				case 'numeric':
				case 'real':
					$data_type = 'number';
					$align = (!$align)? 'right': $align;
					break;
				case 'numrange':
					$data_type = 'numrange';
					$align = (!$align)? 'right': $align;
					break;
				case 'datetime':
				case 'date':
				case 'time':
				case 'timestamp':
				case 'year':
					$data_type = 'datetime';
					$align = (!$align)? 'right': $align;
					break;
				case 'cint':
					//$data_type = 'cnum';
					$align = (!$align)? 'right': $align;
					break;
				default:
					$data_type = 'html';
					$align = (!$align)? 'left': $align;
					break;
			}

			$width = (isset($dd['cols'][$key]['width']) && trim($dd['cols'][$key]['width']) != '')? $dd['cols'][$key]['width']: 'auto';
			if (!strstr($width, '%') && $width != 'auto') {
				$width .= 'px';
			}

			// Column specific CSS
			$table['col_styles'][] = (isset($dd['cols'][$key]['styles']))? $dd['cols'][$key]['styles']: '';
			$table['col_h_styles'][] = (isset($dd['cols'][$key]['h_styles']))? $dd['cols'][$key]['h_styles']: '';

			$tool_tip = '';

			if (isset($dd['cols'][$key]['desc'])) {
				$tool_tip = htmlentities($dd['cols'][$key]['desc'], ENT_QUOTES, 'UTF-8');
			}

			$label = '<strong class="dv_label_text" title="' . $tool_tip . '">';
			$label .= (isset($dd['cols'][$key]['label']))? $dd['cols'][$key]['label'] : $key;

			if (isset($dd['cols'][$key]['unit'])) {
				$label .= '<br /><small>[' . $dd['cols'][$key]['unit'] . ']</small>';
			} elseif (isset($dd['cols'][$key]['units'])) {
				$label .= '<br /><small>[' . $dd['cols'][$key]['units'] . ']</small>';
			}

			$label .= '</strong>';

			$table['col_labels'][] = (isset($dd['cols'][$key]['label']))? $dd['cols'][$key]['label'] : $key;

			$tool_bar = '';

			if (isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'tool' && !$single_record) {
				$name = isset($dd['cols'][$key]['name'])? $dd['cols'][$key]['name']: 'Tool';
				$link_format = explode('{p}', $dd['cols'][$key]['link_format']);
				$param = explode(',', $dd['cols'][$key]['param']);
				$link_zip = $com_name . '/?task=zip&hash_list=';

				$tool_bar .= '<a style="text-decoration: none; margin-left: 2px;" class="dv_tools_launch_multi" title="Launch ' . $name . ' with selected files" target="_blank" href="' . $link_format[0] . '"><img src="' . $html_path . '/run-m.png' . '" />&nbsp;</a>';
				$tool_bar .= '<a style="text-decoration: none;" class="dv_tools_down_multi" title="Download selected files" target="_blank" href="/' . $link_zip . '"><img src="' . $html_path . '/download-m.png' . '" />&nbsp;</a>';
				$tool_bar .= '<input type="checkbox" class="dv_header_select_all" value="' . str_replace('.', '_', $key) . '" title="Select all" />';
			} elseif (isset($dd['cols'][$key]['more_info']) && isset($dd['cols'][$key]['compare']) && !$single_record) {
				$align = 'left';
				$mi = explode('|', $dd['cols'][$key]['more_info']);

				$tool_bar .= '<a style="text-decoration: none;" class="dv_compare_multi" title="' . $dd['cols'][$key]['compare'] . '" target="_blank"  href="/' . $com_name . '/?task=data&format=json&nolimit=true&obj=' . $mi[0] . '&id=">&nbsp;<img src="' . $html_path . '/compare-l.png' . '" />&nbsp;</a>';
				$tool_bar .= '<input type="checkbox" class="dv_header_select_all" value="' . str_replace('.', '_', $key) . '" title="Select all" />';
			} elseif (isset($dd['cols'][$key]['more_info_multi']) && isset($dd['cols'][$key]['compare']) && !$single_record) {
				$align = 'left';
				$mi = explode('|', $dd['cols'][$key]['more_info_multi']);

				$tool_bar .= '<a style="text-decoration: none;" class="dv_compare_multi" title="' . $dd['cols'][$key]['compare'] . '" target="_blank"  href="/' . $com_name . '/?task=data&format=json&nolimit=true&obj=' . $mi[0] . '&id=">&nbsp;<img src="' . $html_path . '/compare-l.png' . '" />&nbsp;</a>';
				$tool_bar .= '<input type="checkbox" class="dv_header_select_all" value="' . str_replace('.', '_', $key) . '" title="Select all" />';
			}

			if ($first_col) {
				$align = (isset($dd['cols'][$key]['align']))? $dd['cols'][$key]['align']: 'left';
				$first_col = false;
			}
			$label = "<div class=\"dv_label css_left\" data-fieldtype=\"$data_type\" >$label $tool_bar</div>";
			$table['aoColumns'][] = array('sTitle' => $label . '&nbsp;&nbsp;&nbsp;&nbsp;', 'sClass'=>$align, 'sType'=>$data_type, 'sWidth'=>$width);
			$table['sColumns'][] = $label;
			$field_types[$key] = $data_type;
		}
	}

	if (isset($dd['order_by'])) {
		$table['aaSorting'] = array();
	}

	$table['sColumns'] = implode(',', $table['sColumns']);
	$table['vis_cols'] = $vis_keys;
	$table['field_types'] = $field_types;

	if (!$data) {
		return json_encode($table);
	}

	// Charts
	if(isset($dd['charts_list'])) {
		$table['charts_list'] = $dd['charts_list'];
	}

	// Maps
	if (isset($dd['maps']) && count($dd['maps']) > 0) {
		$table['maps'] = $dd['maps'];
	}

	// Custom
	if (isset($dd['customizer'])) {
		$table['customizer'] = $dd['customizer'];
	}

	$res_count = 0;
	while ($rec = mysql_fetch_assoc($data)) {

		$row = array();
		foreach($rec as $key => $val) {
			$null_val = false;
			if (!isset($dd['cols'][$key]['hide'])) {

				if ($val != NULL && isset($dd['cols'][$key]['fmt'])) {
					$val = sprintf($dd['cols'][$key]['fmt'], $val);
				}

				if(isset($dd['cols'][$key]['ds_field_type'])) {
					switch($dd['cols'][$key]['ds_field_type']) {
						case '600':	// File
							$dd['cols'][$key]['type'] = 'link';
							$dd['cols'][$key]['ext'] = 'ext';
							$dd['cols'][$key]['link_type'] = 'ds_file';
							break;
						case '601':	// Images
							$dd['cols'][$key]['type'] = 'image';
							$dd['cols'][$key]['image_type'] = 'ds_image';
							break;
						case '602':	// URL
							$dd['cols'][$key]['type'] = 'link';
							$dd['cols'][$key]['full_url'] = 'full_url';
							break;
					}
				}

				if($val == NULL) {
//					if (isset($dd['cols'][$key]['link_label'])) {
//						$val = 	$rec[$dd['cols'][$key]['link_label']];
//					}

					if ($val == '' && isset($dd['cols'][$key]['replace_null'])) {
						$val = $dd['cols'][$key]['replace_null'];
					}

					if ($val == '') {
						$val = '-';
					}

					$null_val = true;

				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'date') {
					$val = strtotime($val);
					$val = date("m/d/Y", $val);
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'image') {
					if (isset($dd['cols'][$key]['linktype']) && $dd['cols'][$key]['linktype'] == 'repofiles') {
						$link = $dd['repo_base'];

						$linkpath = isset($dd['cols'][$key]['linkpath']) ? trim($dd['cols'][$key]['linkpath']) : '';

						if (isset($dd['publication_state'])) {
							// Publication id
							$repo_base = parse_url($dd['repo_base']);
							$repo_base_path = explode('/', $repo_base['path']);
							$pub_id = $repo_base_path[2];
							if (strlen($pub_id) < 5) {
								$pub_id = str_pad($pub_id, 5, '0', STR_PAD_LEFT);
							}

							// Publication version
							$arr = array();
							parse_str($repo_base['query'], $arr);

							$pub_vid = $arr['vid'];
							if (strlen($pub_vid) < 5) {
								$pub_vid = str_pad($pub_vid, 5, '0', STR_PAD_LEFT);
							}

							$link = "/site/publications/$pub_id/$pub_vid/data/";

							$pi = pathinfo($val);

							$linkpath = ($linkpath != '') ? $linkpath . '/' : '';
							$val = $link . $linkpath . $val;

							$small_img = $link . $linkpath  . $pi['filename'] . '_tn.gif';
							$medium_img =  $link . $linkpath . $pi['filename'] . '_medium.gif';

						} else {
							$linkpath = ($linkpath != '') ? '/' . $linkpath : '';
							$val = $link . $linkpath . '&file=' . $val;

							$small_img = $val . '&render=thumb';
							$medium_img = $val . '&render=medium';
						}


					} else {
						$small_img = $val;
						$medium_img = $val;
					}

					$original_img = $val;

					if (isset($dd['cols'][$key]['resized'])) {
						$bn = basename($val);
						$small_img = str_replace($bn, "small/$bn", $val);
						$medium_img = str_replace($bn, "medium/$bn", $val);
					}

					if (isset($dd['cols'][$key]['gallery'])) {

						$path = $rec[$dd['cols'][$key]['gallery']];
						$hash = get_dl_hash($path, 'gallery');
						$gal_url = "/" . $com_name . '/gallery/' . $hash;
						$val = '<a target="_blank" class="dv_gallery_link" href="' . $gal_url . '"><img class="dv_image dv_img_preview" src="' . $small_img . '" data-preview-img="' . $medium_img . '" /></a>';
					} else {
						$val = '<a target="_blank" href="' . $original_img . '"><img class="dv_image dv_img_preview" src="' . $small_img . '" data-preview-img="' . $medium_img . '"  alt="Preview image for : ' . htmlentities($original_img) . '"/></a>';
					}
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'email') {
					$val = '<a href="mailto:' . $val . '">' . $val . '</a>';
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'link') {
					if (!isset($dd['cols'][$key]['multi'])) {
						$preview = '';
						if (isset($dd['cols'][$key]['preview'])) {
							$preview = 'data-preview-img="' . $rec[$dd['cols'][$key]['preview']] . '"';
						}

						if (isset($dd['cols'][$key]['linktype']) && $dd['cols'][$key]['linktype'] == 'repofiles') {
							$link = $dd['repo_base'];

							$linkpath = isset($dd['cols'][$key]['linkpath']) ? trim($dd['cols'][$key]['linkpath']) : '';

							if (isset($dd['publication_state'])) {
								$linkpath = ($linkpath != '') ? $linkpath . '/' : '';
								$val = $link . '&file=' . $linkpath . $val;
							} else {
								$linkpath = ($linkpath != '') ? '/' . $linkpath : '';
								$val = $link . $linkpath . '&file=' . $val;
							}


							$dd['cols'][$key]['link_label'] = $key;
							$dd['cols'][$key]['relative'] = 'relative';
						}
						$val = dv_to_link($rec, $key, $dd, $val, $preview);
					} else {
						$sep = isset($dd['cols'][$key]['sep'])? $dd['cols'][$key]['sep']: ',';
						$multi_val = explode($sep, $val);


						if (isset($dd['cols'][$key]['preview'])) {
							$prv_list = explode($sep, $rec[$dd['cols'][$key]['preview']]);
						}

						$links = array();

						for($i=0; $i<count($multi_val); $i++) {
							$preview = '';
							if(isset($prv_list[$i])) {
								$preview = 'data-preview-img="' . trim($prv_list[$i]) . '"';
							}

							$links[] = dv_to_link($rec, $key, $dd, trim($multi_val[$i]), $preview);
						}
						$val = implode('<br />', $links);
					}
				} elseif(isset($dd['cols'][$key]['type']) && $dd['cols'][$key]['type'] == 'tool') {
					$label = isset($dd['cols'][$key]['link_label'])? $rec[$dd['cols'][$key]['link_label']]: basename($rec[$dd['cols'][$key]['dl']], '.csv');
					$name = isset($dd['cols'][$key]['name'])? $dd['cols'][$key]['name']: 'Tool';

					$tool_link = '';
					$dl = '';
					$check = '';

					if (isset($dd['cols'][$key]['dl']) && file_exists($rec[$dd['cols'][$key]['dl']])) {
						$link_format = explode('{p}', $dd['cols'][$key]['link_format']);
						$param = explode(',', $dd['cols'][$key]['param']);
						$tl = '';
						for ($i = 0; $i < count($param); $i++) {
							$tl .= $link_format[$i] . $rec[$param[$i]];
						}
						$tool_link = '<a class="dv_tools_launch_link" title="Launch ' . $name . '" target="_blank" href="' . $tl . '"><img src="' . $html_path . '/run.png' . '" /></a>';

						if (isset($dd['cols'][$key]['dl'])) {
							$path = $rec[$dd['cols'][$key]['dl']];
							$link = '';
							if (strpos($path, JPATH_BASE) === 0) {
								$link = substr($path, strlen(JPATH_BASE)+1);
							} else {
								$hash = get_dl_hash($path);
								$link = $com_name . '/?task=file&hash=' . $hash;
							}
							$dl .= '<a title="Download File" data-data-file="' . $path . '" class="dv_tools_dl_link" target="_blank" href="/' . $link . '"><img src="' . $html_path . '/download.png' . '" /></a>';
						}

						$check = '<input type="checkbox" class="' . str_replace('.', '_', $key) . '" value="' . $path . '" style="float: right;" />';
					} else {
						$tool_link = '<span class="hand" title="File is missing or not uploaded yet."><img src="' . $html_path . '/run.png' . '" /></span>';
						$dl = '<span class="hand" title="File is missing or not uploaded yet."><img src="' . $html_path . '/download.png' . '" /></span>';
						$check = '<input title="File is missing or not uploaded yet." disabled="disabled" type="checkbox" value="" style="float: right;" />';
					}

					$val = $label . '&nbsp;&nbsp;&nbsp;' . $tool_link . '&nbsp;&nbsp;' . $dl . '&nbsp;&nbsp;' . $check;
				}

				if (isset($dd['cols'][$key]['numrange'])) {
					$min = $rec[$dd['cols'][$key]['numrange']['min']];
					$max = $rec[$dd['cols'][$key]['numrange']['max']];
					$range = ($min == $max)? $min: "$min to $max";
					$val = "<span data-min='$min' data-max='$max' class='range-data'>$range</span>";
				}

				if (isset($dd['cols'][$key]['more_info']) && !$null_val) {
					$mi = explode('|', $dd['cols'][$key]['more_info']);
					$obj = '&obj=' . $mi[0];
					$id = (isset($mi[1]))? '&id=' . $rec[$mi[1]]: '';

					$check = '';
					if (isset($dd['cols'][$key]['compare']) && !$single_record) {
						$check = '<input data-colname= "' . str_replace('.', '_', $key) . '" type="checkbox" class="' . str_replace('.', '_', $key) . ' dv_compare_chk" value="' . $rec[$mi[1]] . '" style="float: right;" />';
					}

					$val = '<a title="Click to view more information about this item" class="more_info" href="/' . $com_name . '/?task=data' . $obj . $id . '&format=json" style="color: blue;">' . $val . '</a>' . '&nbsp;&nbsp;' . $check;
				} elseif (isset($dd['cols'][$key]['more_info_multi']) && !$null_val) {
					$mi = explode('|', $dd['cols'][$key]['more_info_multi']);
					$obj = '&obj=' . $mi[0];
					$id = (isset($mi[1]))? '&id=' . $rec[$mi[1]]: '';

					$check = '';
					if (isset($dd['cols'][$key]['compare']) && !$single_record) {
						$check = '<input data-colname= "' . str_replace('.', '_', $key) . '" type="checkbox" class="' . str_replace('.', '_', $key) . ' dv_compare_chk" value="' . $rec[$mi[1]] . '" style="float: right;" />';
					}

					$val = '<a title="Click to view more information about this item" class="more_info_multi" href="/' . $com_name . '/?task=data' . $obj . $id . '&format=json" style="color: blue;">' . $val . '</a>' . '&nbsp;&nbsp;' . $check;
				}

				if (isset($dd['cols'][$key]['filtered_view']) && !$null_val) {
					$fv = $dd['cols'][$key]['filtered_view'];

					$filter = array();
					if (isset($fv['filter'])) {
						foreach ($fv['filter'] as $c => $k) {
							$filter[] = "$c|" . urlencode((isset($rec[$k])? $rec[$k]: $k));
						}
						$filter = '?filter=' . implode('||', $filter);
					} else {
						$filter = '';
					}

					$append_to_url = isset($fv['append_to_url'])? $fv['append_to_url']: '';
					$fv_data = array_key_exists($fv['data'], $rec)? $rec[$fv['data']]: $fv['data'];

					if ($fv_data != NULL) {
						$val = '<a class="filtered_view" title="View filtered spreadsheet" target="_blank" href="/' .$com_name . '/' . $fv['view'] . '/' . $fv_data . '/' . $filter . $append_to_url . '#dv_top">' . $val . '</a>';
					}
				}

				if (isset($dd['cols'][$key]['launch_view']) && !$null_val) {
					$fv = $dd['cols'][$key]['filtered_view'];
					$filter = array();
					foreach ($fv['filter'] as $c => $k) {
						$filter[] = "$c|" . (isset($rec[$k])? $rec[$k]: $k);
					}
					$filter = '?filter=' . implode('||', $filter);
					$append_to_url = isset($fv['append_to_url'])? $fv['append_to_url']: '';

					$val = '<a class="filtered_view" title="View filtered spreadsheet" target="_blank" href="/' .$com_name . '/' . $fv['view'] . '/' . $fv['data'] . '/' . $filter . $append_to_url . '#dv_top">' . $val . '</a>';
				}

				if (isset($dd['cols'][$key]['abbr'])) {
					$title = 'title="' . str_replace('"', '&#34;', $rec[$dd['cols'][$key]['abbr']]) . '"';

					$val = '<span class="quick_tip hand" ' . $title . '>' . $val . '</span>';
				}

				$opmod_style = '';
				$opmod_title = '';

				if (isset($dd['cols'][$key]['opmod'])) {	// Only doing text style now, More to come....
					$switch = $rec[$dd['cols'][$key]['opmod']['switch']];
					foreach($dd['cols'][$key]['opmod']['case'] as $is_val=>$func) {
						if ($switch == $is_val) {
							$func = explode('|', $func);
							$param = $func[1];
							$func = 'dv_opmod_' . $func[0];
							$opmod_style = $func($param);
							$opmod_title = htmlspecialchars($is_val);
							break;
						}
					}
				}

				if(isset($dd['cols'][$key]['width'])) {

					$nowrap = '';
					if (isset($dd['cols'][$key]['nowrap'])) {
						$nowrap = 'white-space: nowrap;';
					}
					$extra_style = '';
					$title = $opmod_title;
					$class = '';
					$label = isset($dd['cols'][$key]['label'])? $dd['cols'][$key]['label']: $key;
					if ($val != "-" && isset($dd['cols'][$key]['truncate'])) {
						$title = $val;
						$title = " title=" . '"' . strip_tags(str_replace('"', '&#34;', $val)) . '" ';
						$class = 'class="truncate hand"';

						$val = '<span style="white-space: nowrap;"><span ' . $title . $class . ' style="' . $opmod_style . ' width: ' . $dd['cols'][$key]['width'] . 'px;">' . $val . '</span><span class="truncateafter">&nbsp;</span></span>';
					} elseif ($val != "-" && isset($dd['cols'][$key]['height'])) {
						$title = $val;
						$title = " title=" . '"' . strip_tags(str_replace('"', '&#34;', $val)) . '" ';
						$class = 'class="truncate hand scrollcell"';

						$val = '<div ' . $title . $class . ' style="' . $nowrap . $opmod_style . ' width: ' . $dd['cols'][$key]['width'] . 'px; max-height: ' . $dd['cols'][$key]['height'] . 'px; overflow: clip;">' . $val . '</div>';
					} else {
						$val = '<p ' . $title . ' ' . $class . ' style="' . $nowrap . $opmod_style . ' width: ' . $dd['cols'][$key]['width'] . 'px;">' . $val . '</p>';
					}
				} elseif ($opmod_style != '') {
					$val = '<span title="' . $opmod_title . '" class="hand" style="' . $opmod_style . '">' . $val . '</span>';
				}

				$row[] = $val;
			}
		}
		$res_count++;
		$table['aaData'][] = $row;
	}

	return json_encode($table);
}

function dv_to_link($rec, $key, $dd, $val, $preview)
{
	global $com_name, $html_path;
	$nowarp = isset($dd['cols'][$key]['nowrap'])? 'style="white-space: nowrap;"': '';
	$path = $val;
	$link = '';
	$label = isset($dd['cols'][$key]['link_label'])? $rec[$dd['cols'][$key]['link_label']]: false;

	$path_prefix = '';
	if (isset($dd['cols'][$key]['link_type']) && $dd['cols'][$key]['link_type'] == 'ds_file') {
		$path = '/' . $dd['ds_id'] . '/' . $dd['table'] . '/' . str_replace($dd['table'] . '.', '', $key) . '/' . $val;
		$_SESSION['dv']['allowed_files'][$path] = 1;
		$link = '/' . $com_name . '/?task=files&path=' . $path;
		if (!$label) {
			$pi = pathinfo($path);
			$label = isset($pi['filename'])? $pi['filename']: (isset($url['host'])? $url['host']: false);
			if (isset($pi['extension'])) {
				$label .= '.' . $pi['extension'];
			}
		}
	} elseif (strpos($path, 'http') === 0) {
		$link = $path;
		$url = parse_url($link);
		if (!$label) {
			$pi = pathinfo($path);
			$label = isset($pi['filename'])? $pi['filename']: (isset($url['host'])? $url['host']: false);
			if (isset($pi['extension'])) {
				$label .= isset($dd['cols'][$key]['ext'])? '.' . $pi['extension']: '';
			}
		}
	} elseif (strpos($path, JPATH_BASE) === 0) {
		$link = substr($path, strlen(JPATH_BASE));
		if (!$label) {
			$pi = pathinfo($path);
			$label = isset($pi['filename'])? $pi['filename']: false;
			if (isset($pi['extension'])) {
				$label .= isset($dd['cols'][$key]['ext'])? '.' . $pi['extension']: '';
			}
		}
	} elseif (strpos($path, '/site/') === 0) {
		$link = $path;
		if (!$label) {
			$pi = pathinfo($path);
			$label = isset($pi['filename'])? $pi['filename']: false;
			if (isset($pi['extension'])) {
				$label .= isset($dd['cols'][$key]['ext'])? '.' . $pi['extension']: '';
			}
		}
	} elseif (isset($dd['cols'][$key]['relative'])) {
		$link = $path;
		if (!$label) {
			$pi = pathinfo($path);
			$label = isset($pi['filename'])? $pi['filename']: false;
			if (isset($pi['extension'])) {
				$label .= isset($dd['cols'][$key]['ext'])? '.' . $pi['extension']: '';
			}
		}
	} else {
		$hash = get_dl_hash($path);
		$link = "/" . $com_name . '/?task=file&hash=' . $hash;
		if (!$label) {
			$pi = pathinfo($path);
			$label = isset($pi['filename'])? $pi['filename']: false;
			if (isset($pi['extension'])) {
				$label .= isset($dd['cols'][$key]['ext'])? '.' . $pi['extension']: '';
			}
		}
	}

	if (!$label || isset($dd['cols'][$key]['full_url'])) {
		$label = $link;
	}

	$link_target = '_blank';
	if (isset($dd['cols'][$key]['link_target'])) {
		switch ($dd['cols'][$key]['link_target']) {
			case 'self':
				$link_target = '_self';
				break;
			case 'blank':
				$link_target = '_blank';
				break;
			case 'parent':
				$link_target = '_parent';
				break;
			case 'top':
				$link_target = '_top';
				break;
		}
	}

	if ($preview != '') {
		$val = "<a $nowarp title=\"$link\" target=\"$link_target\" href=\"" . str_replace('#', '%23', $link) . "\" $preview class=\"dv_img_preview\">$label</a>";
	} else {
		$val = "<a $nowarp title=\"$link\" target=\"$link_target\" href=\"" . str_replace('#', '%23', $link) . "\" >$label</a>";
	}

	return $val;
}


// HTML data output modifires
function dv_opmod_set_color($param)
{
	return "color: $param;";
}

?>