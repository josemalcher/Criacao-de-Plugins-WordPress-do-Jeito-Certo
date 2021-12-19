# Criação de Plugins WordPress do Jeito Certo

https://www.udemy.com/course/crie-plugins-wordpress-do-jeito-certo/



## <a name="indice">Índice</a>

1. [Seção 1: Introdução](#parte1)     
2. [Seção 2: Antes de Começar](#parte2)     
3. [Seção 3: Projeto #1 - MV Slider](#parte3)     
4. [Seção 4: Projeto #2 - MV Testimonials](#parte4)     
5. [Seção 5: Projeto #3 - MV Translations](#parte5)     
6. [Seção 6: Conclusão](#parte6)     
---


## <a name="parte1">1 - Seção 1: Introdução</a>

```text
Arquivos e outros materiais
Você pode ter acesso a todos os códigos e materiais extras para este curso clicando no seguinte link:

https://drive.google.com/file/d/1gRj3r20fcPpSyYw7o09oA_R2Gf9in4sA/view?usp=sharing

Assista o vídeo abaixo pra ver um exemplo de como baixar o material:

https://www.youtube.com/watch?v=L0MRLdonb14

Como fazer?

Baixe e extraia o arquivo compactado para o seu computador. O material é todo organizado por aula.

Use o material para comparar o seu código como o meu quando necessário.

Algumas aulas possuem material inicial que deve ser copiado e colado para acompanhar o conteúdo. Siga as instruções das aulas para utilizar o material correto.




Como enviar material de teste para o instrutor?
Siga as instruções deste vídeo: https://www.youtube.com/watch?v=t_gDIZvLnq8



Projetos Finais do Curso
Acesse os arquivos com todos os projetos deste curso no GitHub:

https://github.com/marceloquinze/Projetos-do-Curso-Plugins-WordPress
```

[Voltar ao Índice](#indice)

---


## <a name="parte2">2 - Seção 2: Antes de Começar</a>

- [https://developer.wordpress.org/reference/](https://developer.wordpress.org/reference/)


**WordPress Flow**
```text

load wp-config.php
set up default constants
load wp-content/advanced-cache.php if it exists
load wp-content/db.php if it exists
connect to mysql, select db
load object cache (object-cache.php if it exists, or wp-include/cache.php if not)
load wp-content/sunrise.php if it exists (multisite only)
load l10n library
load mu plugins
DO_ACTION 'muplugins_loaded' (only accessible to mu plugins)
load active plugins
load pluggables.php
DO_ACTION 'plugins_loaded' (first hook available to plugins)
load rewrite rules
instantiate $wp_query, $wp_rewrite and $wp.
	$wp_query is a global instance of the WP_Query class. For more info, see ANY QUERY
	$wp_rewrite is a global instance of the WP_Rewrite class and contains our rewrite rules and functions
	$wp is a global instance of the WP class and contains the functions that will parse our request and perform the main query (see REQUEST)
DO_ACTION 'setup_theme'
include child theme functions.php
include parent theme functions.php
DO_ACTION 'after_setup_theme' (first hook available to themes)
set up current user object
DO_ACTION 'init'
register widgets (DO_ACTION 'widget_init')
call wp() (which calls $wp->main())


REQUEST
=======

$wp->parse_request()
	loop over rewrite rules to find a match
	APPLY_FILTERS 'query_vars' to the publicly available query vars
	fill query vars with $_POSTs, $_GETs, and rewritten vars
	APPLY_FILTERS 'request' to the request variables
	DO_ACTION_REF_ARRAY 'parse_request' with array of request vars (query vars, request, matched rewrite rules, etc)

DO_ACTION_REF_ARRAY 'send_headers' with the 'WP' object.

THE MAIN QUERY
==============

$wp->query_posts()
	goto ANY QUERY

if posts are empty, set is_404() (and send 404 headers)
set all the query_vars to global variables
DO_ACTION_REF_ARRAY 'wp' with the main WP object


	ANY QUERY
	=========

	WP_Query->query( query vars )
		WP_Query->parse_query( query vars )
			build query parameters based off query vars
			set WP_Query->is_* vars based off query parameters
				if this query is $wp_the_query then these determine the values of the global is_*() functions too
			DO_ACTION_REF_ARRAY 'parse_query' with WP_Query object (query parameters, query vars, conditionals)
		WP_Query->get_posts()
			DO_ACTION_REF_ARRAY 'pre_get_posts' with WP_Query object
			APPLY_FILTERS_REF_ARRAY 'posts_search' with search SQL
			series of APPLY_FILTERS on the query SQL (if suppress_filters=false):
			 * posts_where
			 * posts_join
			 * posts_where_paged
			 * posts_groupby
			 * posts_join_paged
			 * posts_orderby
			 * posts_distinct
			 * post_limits
			 * posts_fields
			 * posts_clauses
			APPLY_FILTERS_REF_ARRAY 'posts_request' (if suppress_filters=false)
			fetch posts from the database
			APPLY_FILTERS_REF_ARRAY 'posts_results' (if suppress_filters=false)
			prepend sticky posts
			APPLY_FILTERS_REF_ARRAY 'the_posts' (if suppress_filters=false)
			return posts

TEMPLATE
========

DO_ACTION 'template_redirect'
if is_feed()
	load the feed template
else
	look for template file in theme based on template hierarchy
	APPLY_FILTERS 'template_include'
	load the template file (which usually runs a loop @TODO document a loop)
DO_ACTION 'shutdown'
```
FONTE: https://gist.github.com/johnbillion/4fa3c4228a8bb53cc71d


- [https://codex.wordpress.org/Plugin_API/Filter_Reference](https://codex.wordpress.org/Plugin_API/Filter_Reference)

 -[wp-proj01/wp-content/themes/twentynineteen/functions.php](wp-proj01/wp-content/themes/twentynineteen/functions.php)

```php

function add_div_tag_before() {
	?>
    <div class="test-div">THE LOOP STARTED
		<?php
		}
		add_action( 'loop_start', 'add_div_tag_before', 10 );

		function add_div_tag_after(){
		?>
        THE LOOP ENDED!
    </div>
	<?php
}
add_action( 'loop_end', 'add_div_tag_after', 11 );

function modify_content( $content ) {
	return $content . 'COnteudo Adicionado por Filtros!!!';
}

add_filter( 'the_content', 'modify_content' );

/*
   function modify_body_classes( $classes, $class ) {
	if ( is_single() ) {
		$class[] = 'test_add_single';
		$classes = array_merge( $classes, $class );
	}

	return $classes;
}*/
function modify_body_classes( $classes, $class = null ) {
	if ( is_single() ) {
		$classes[] = 'test_add_single';
	}

	return $classes;
}

add_filter( 'body_class', 'modify_body_classes', 10, 2 );

```



[Voltar ao Índice](#indice)

---


## <a name="parte3">3 - Seção 3: Projeto #1 - MV Slider</a>



[Voltar ao Índice](#indice)

---


## <a name="parte4">4 - Seção 4: Projeto #2 - MV Testimonials</a>



[Voltar ao Índice](#indice)

---


## <a name="parte5">5 - Seção 5: Projeto #3 - MV Translations</a>



[Voltar ao Índice](#indice)

---


## <a name="parte6">6 - Seção 6: Conclusão</a>



[Voltar ao Índice](#indice)

---

