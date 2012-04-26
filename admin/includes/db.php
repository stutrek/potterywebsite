<?
$db = null;
function connect() {
	global $db;
	if( $db == null ) {
		$db = mysql_pconnect( DB_SERVER, DB_USER, DB_PASSWORD ) or die( 'Cannot connect to server' );
		mysql_select_db( DB, $db ) or die('Cannot connect to Database');
	}
}
function query( $query, $debug=false ) {
	global $db, $database_delay;
	
	if( $database_delay ) {
		//echo 'waiting';
		sleep( 0.10 );
	}
	connect();
	
	$query = trim( $query );
	if( $debug == 'query' or $debug === true or DEBUG_VERBOSE == true) {
		echo "<br />\n".$query."<br />\n";
	}
	$type = strtok( $query, ' ' );
	$result = mysql_query( $query, $db );
	if( $result == false ) {
		if( DEBUG_ERRORS or DEBUG_VERBOSE or $_SESSION['debug'] ) {
			echo '<br />Error querying '.SITE_NAME.' database.<br /> '.$query."<br /><br />\n".mysql_error();
			$debug_backtrace = debug_backtrace();
			foreach( $debug_backtrace as $key => $val ) {
				echo "<br />\n <strong>".$key.'. line '.$val['line'].'</strong> of <strong>'.$val['file'].'</strong><br />'."\n";
			}
			
		} else {
			echo 'Database error. Set DEBUG_ERRORS to true in the constants file for more information.';
		}
		exit;
	}
	switch( strtolower( $type ) ) {
		case 'select':	
			$return = array();
			while( $row = mysql_fetch_assoc( $result ) ) {
				$return[] = $row;
			}
			break;
		case 'insert':
			$return = mysql_insert_id();
			break;
		case 'update':
		case 'delete':
			$return = mysql_affected_rows();
			break;
		default:
			return $result;
	}
	if( $debug == 'result' or $debug === true ) {
		dump( $return );
	}
	return $return;
}

function select( $table, $where='', $select='*', $limit='', $order='', $group='', $debug=false ) {
	connect();
	
	if( is_string( $table ) and ( strpos( $table, ',') !== false or strpos( $table, ' ' ) !== false ) ) {
		$table = preg_replace( '/([ ,]{1,})/', '`\\1`', $table );
	}
	
	if( is_array( $table ) ) {
		foreach( $table as $key => $val ) {
			if( substr( $val, 0, 4 ) == 'sql:' ) {
				$table[$key] = substr( $val, 4 );
			} elseif( strpos( $val, ',') !== false or strpos( $val, ' ' ) !== false ) {
				$table[$key] = '`'.preg_replace( '/([ ,]{1,})/', '`\\1`', $val ).'`';
			}
		}
		$table = implode( ', ', $table );
	}
	
	$where = make_where( $where );
	if( $where != '' ) {
		$where = 'WHERE '.$where;
	}
	if( $limit != null ) {
		if( $limit == 1 ) {
			$limit_text = 'LIMIT 0, 1';
		} else {
			$limit_text = 'LIMIT '.$limit;
		}
	}
	if( $order != null ) {
		$order_text = 'ORDER BY '.$order;
	}
	if( $group != '' ) {
		if( substr( $group, 0, 4 ) == 'sql:' ) {
			$group_text = 'GROUP BY '.substr( $group, 4 );
		} elseif( strpos( $group, ',' ) ) {
			$group_a = preg_split( '/(\W)*[,](\W+)*/', $group );
			$group = implode( '`, `', $group_a );
			$group_text = 'GROUP BY `'.$group.'`';
		} else {
			$group_text = 'GROUP BY `'.str_replace( '.', '`.`', $group).'`';
		}
	}
	if( $select == '' or $select == null ) {
		$select = '*';
	} elseif( is_array( $select ) ) {
		$select = implode( ', ', $select );
	}
	
	$return = query( "SELECT $select FROM `$table` $where $group_text $order_text $limit_text;", $debug );
	//If we were only asked for one row, or for a count we only return that.
	if( $limit == 1 or ( ( substr( $select, 0, 6 ) == 'count(' or substr( $select, 0, 4 ) == 'avg(' ) and strpos( $select, ',' ) === false ) ) {
		if( $debug == true ) {
			echo 'removing data from array<br />\n';
		}
		$return = $return[0];
		//If the select was only one item we only return that one item.
		if( strpos( $select, ',' ) == false and $select != '*' and is_array( $return ) ) {
			$keys = array_keys( $return );
			$select_key = $keys[0];
			$return = $return[$select_key];
		}
	} elseif( strpos( $select, ',' ) == false and $select != '*' and is_array( $return[0] ) ) {
		$keys = array_keys( $return[0] );
		$select_key = $keys[0];
		foreach( $return as $key => $val ) {
			$return[$key] = $val[$select_key];
		}
	}
	if( $debug != false ) {
		dump( $return );
	}
	return $return;
	
}
//Table is a string
//Changes is either an array ( column => value ) or a string
//Where is a string (containing '=') or a number, which will be used as the id.
function update( $table, $changes, $where, $limit='', $debug=false ) {
	connect();
	
	if( count( $changes ) == 0 ) {
		return 0;
	}
	$changes_string = '';
	if( is_array( $changes ) ) {
		foreach( $changes as $col => $val ) {
			if( substr( $val, 0 ,4 ) == 'sql:' ) {
				$val = substr( $val, 4 );
				$changes_string .= " `$col`=$val,";
			} else {
				$changes_string .= " `$col`='".mysql_real_escape_string( stripslashes(trim($val)) )."',";
			}
		}
		$changes = substr( $changes_string, 0, strlen( $changes_string )-1);
	}
	
	$where_string = make_where( $where );
	if( $where_string != '' ) {
		$where = 'WHERE '.$where_string;
	}
	if( $limit != '' ) {
		$limit = 'LIMIT '.$limit;
	}
	$query = "UPDATE `$table` SET $changes $where $limit;";
	return query( $query, $debug );
}
function insert( $table, $values, $debug=false ) {
	connect();
	$values_string = '';
	if( is_array( $values ) ) {
		foreach( $values as $col => $val ) {
			if( substr( $val, 0 ,4 ) == 'sql:' ) {
				$val = substr( $val, 4 );
				$values_string .= " `$col`=$val,";
			} else {
				$values_string .= " `$col`='".mysql_real_escape_string( stripslashes(trim($val)) )."',";
			}
		}
		$values = substr( $values_string, 0, strlen( $values_string )-1);
	}	
	if( $values != '' ) {
		return query("INSERT INTO `$table` SET $values;", $debug );
	} else {
		echo "Error inserting into `$table`; no values!";
		dump( debug_backtrace() );
	}
}
function delete( $table, $where, $limit='', $debug=false ) {
	connect();
	$where_string = make_where( $where );
	if( $where_string != '' ) {
		$where = 'WHERE '.$where_string;
	} else {
		echo 'Error! Deleting without a where clause!';
		return;
	}
	if( $limit != null ) {
		$limit = 'limit '.$limit;
	}
	$query = "DELETE FROM `$table` $where $limit;";
	if( $debug ) {
		echo $query;
	}
	return query( $query, $debug );

}

function make_where( $where ) {
	global $db;
	connect();
	if( is_array( $where ) ) {
		foreach( $where as $col => $val ) {
			if( strpos( $col, ' ' ) !== false ) {
				$col_a = explode( ' ', $col );
				$compare = $col_a[count($col_a)-1];
				switch( strtolower( $compare ) ) {
					case '!=':
					case '>':
					case '<':
					case '>=':
					case '<=':
					case 'like':
					case 'between':
					case '<>':
					case '<=>':
					case '=':
					case 'in':
						unset( $col_a[count($col_a)-1] );
						$col = implode( ' ', $col_a );
						break;
					default:
						$compare = '=';
				}
			} else {
				$compare = '=';
			}
			
			if( is_array( $val ) ) {
				if( count( $val ) > 0 ) {
					$or_a = array();
					foreach( $val as $col2 => $val2 ) {
						if( is_array( $val2 ) ) {
							$val2 = make_where( $val2 );
							$is_recursion = true;
						}
						if( is_numeric( $col2 ) ) {
							$col2 = $col;
							$compare2 = $compare;
						} elseif( strpos( $col2, ' ' ) !== false ) {
							$col_a = explode( ' ', $col2 );
							$compare2 = $col_a[count($col_a)-1];
							switch( strtolower( $compare2 ) ) {
								case '!=':
								case '>':
								case '<':
								case '>=':
								case '<=':
								case 'like':
								case 'between':
								case '<>':
								case '<=>':
								case '=':
								case 'in':
									unset( $col_a[count($col_a)-1] );
									$col2 = implode( ' ', $col_a );
									break;
								default:
									$compare2 = '=';
							}
						} else {
							$compare2 = '=';
						}
						
						if( strpos( $col2, '(' ) !== false ) {
							//do nothing
						} elseif( strpos( $col2, '.' ) != false  ) { 
							$col_a2 = explode( '.', $col2 );
							$col2 = '`'.implode( '`.`', $col_a2 ).'`';
						} else {
							$col2 = '`'.$col2.'`';
						}
						if( substr( $val2, 0, 4 ) == 'sql:' ) {
							$or_a[] = $col2.' '.$compare2.' '.substr( $val2, 4 );
						} elseif( $is_recursion ) {
							$or_a[] = '('.$val2.')';
						} else {
							$or_a[] = $col2.' '.$compare2.' \''.mysql_real_escape_string( $val2, $db ).'\'';
						}
					}
					$where_string .= ' ('.implode( ' OR ', $or_a ).' ) AND';
				}
			} elseif( !is_numeric( $col ) ) {
			
				if( strpos( $col, '(' ) !== false ) {
					//do nothing
				} elseif( strpos( $col, '.' ) !== false  ) { 
					$col_a = explode( '.', $col );
					$col = '`'.implode( '`.`', $col_a ).'`';
				} else {
					$col = '`'.$col.'`';
				}
				if( substr( $val, 0 ,4 ) == 'sql:' ) {
					$val = substr( $val, 4 );
					$where_string .= " $col $compare $val AND";
				} else {
					$where_string .= " $col $compare '".mysql_real_escape_string( $val )."' AND";
				}
			}
		}
		$where = substr( $where_string, 0, strlen($where_string)-4 );
	}
	return $where;
}

function make_search_query( $search_s, $column='search_text', $use_regexp=false ) {
	
	if( trim( $search_s ) == '' ) {
		return '';
	}
	
	$search_s = search_text( $search_s, true );
	
	$search_a = preg_split( '/(?=([^"]*"[^"]*")*(?![^"]*")) /', $search_s );
	
	$search_sql_a = array();
	
	foreach( $search_a as $keyword ) {
		if( trim( $keyword != '' ) ) {
			if( $keyword[0] == '"') {
				$keyword = substr( $keyword, 1, strlen($keyword)-2 );
				$value = 10;
			} else {
				$value = 3;
			}
			if( $use_regexp ) {
				$keyword = preg_replace( '/[\W\S_]/', ' ', $keyword );
			}
			$search_sql_a[] = '( ( ( '.$column.' like "% '.$keyword.' %" or '.$column.' like "'.$keyword .' %" or '.$column.' like "% '.$keyword.'" or '.$column.' = "'.$keyword.'") = 1) * '.$value.' )';
			$search_sql_a[] = '( ( ( ( '.$column.' like "%'.$keyword.'%" ) = 1) + (('.$column.' like "%'.$keyword.' %") = 1) + (('.$column.' like"% '.$keyword.'%") = 1) ) * '.( $value / 3 ).' )';
		}
	}
	$search_sql_a[] = '( ( '.$column.' = "'.str_replace( '"', '', $search_s ).'" ) * 50 ) ';
	$search_sql = implode( ' + ', $search_sql_a );
	
	return $search_sql;
}

function search_text( $string, $preserve_quotes=false ) {
	$string = remove_accents( $string );
	$string = preg_replace( "/((\w)('))*s(\W|$)/", '\\1 ', $string );
	if( $preserve_quotes ) {
		$string = ereg_replace( '[^0-9a-zA-Z"]', ' ', $string );
	} else {
		$string = ereg_replace( '[^0-9a-zA-Z]', ' ', $string );
	}
	$string = ereg_replace( '( ){1,}', ' ', $string );
	return trim( $string );
}


function table( $array ) {
	$array = array_values( $array );
	
	if( count( $array ) > 0 ) {
		
		$keys = array_keys( $array[0] );
		
		echo '<table><tr class="table_label">';
		foreach( $keys as $key ) {
			echo '<td>'.$key.'</td>';
		}
		echo '</tr>';
		$odd = true;
		foreach( $array as $row ) {
			if( $odd ) {
				echo '<tr class="table_odd_row">';
			} else {
				echo '<tr class="table_even_row">';
			}
			$odd = !$odd;
			foreach( $row as $value ) {
				echo '<td>'.$value.'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}
}

function tall_table( $array ) {
	if( is_array( $array ) ) {
		
		echo '<table>';
		$odd = true;
		foreach( $array as $key => $val ) {
			if( $odd ) {
				echo '<tr class="table_odd_row">';
			} else {
				echo '<tr class="table_even_row">';
			}
			$odd = !$odd;
			echo '<td>'.$key.'</td><td>'.$val.'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
}

function dump( $var, $label=null ) {
	if( $var === '' or $var === null or $var === 0 or $var === false ) {
		var_dump( $var );
		return;
	}
	if( $label ) {
		echo '<strong>'.$label.'</strong><br />';
	}
	echo '<pre style="text-align: left; font-size: 10px;">';
	print_r( $var );
	echo '</pre>';
}

function dump_array( $array, $tabs=1 ) {
	$html = "<pre style=\"text-align: left; font-size: 10px;\">array( \n";
	foreach( $array as $key => $val ) {
		$html .= str_repeat( "\t", $tabs )."'$key' => ";
		if( is_array( $val ) ) {
			$html .= dump_array( $array, $tabs+1 );
		} else {
			$html .= "'$val',\n";
		}
	}
	$html .= ')</pre>';
	return $html;
}

function excel( $array ) {
	$array = array_values( $array );
	
	$keys = array_keys( $array[0] );
	
	foreach( $keys as $key ) {
		echo $key."\t";
	}
	echo "\n";
	
	foreach( $array as $row ) {
		foreach( $row as $value ) {
			echo $value."\t";
		}
		echo "\n";
	}
}

function form_table( $array ) {
	if( is_array( $array ) ) {
		echo '<table>';
		$odd = true;
		foreach( $array as $key => $val ) {
			if( $odd ) {
				echo '<tr class="table_odd_row">';
			} else {
				echo '<tr class="table_even_row">';
			}
			$odd = !$odd;
			echo '<td class="input_label">'.$key.'</td><td><input type="text" name="'.$key.'" value="'.$val.'" /></td>';
			echo '</tr>';
		}
		echo '<td></td><td><input type="submit" value="submit" /></td></tr></table>';
	}
}

?>