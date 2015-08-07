var CustomRelatedPosts = CustomRelatedPosts || {};

CustomRelatedPosts.post_id = undefined;
CustomRelatedPosts.search_term_field = undefined;
CustomRelatedPosts.search_button = undefined;

CustomRelatedPosts.initMetaBox = function() {
    CustomRelatedPosts.post_id = jQuery('#crp_post').val();
    CustomRelatedPosts.search_term_field = jQuery('#crp_search_term');
    CustomRelatedPosts.search_button = jQuery('#crp_search_button');

    CustomRelatedPosts.search_button.on('click', function() { CustomRelatedPosts.searchPosts() } );
    CustomRelatedPosts.search_term_field.on('keydown', function(e) {
        if(e.keyCode == 13) {
            e.preventDefault();
            e.stopPropagation();
            CustomRelatedPosts.searchPosts();
        }
    });

    jQuery('#crp_relations').on('click', '.crp_remove_relation', function() {
        CustomRelatedPosts.removeRelation(jQuery(this));
    });

    jQuery('#crp_search_results_table').on('click', 'button', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
};

CustomRelatedPosts.removeRelation = function(btn) {
    var both = btn.hasClass('crp_remove_relation_both'),
        to = btn.hasClass('crp_remove_relation_to'),
        from = btn.hasClass('crp_remove_relation_from'),
        post_id = btn.parent('div').data('post');

    if(post_id == undefined) {
        post_id = btn.parents('tr').data('post');

        if(to) {
            var title = btn.parents('tr').find('td').first().text();
            var element = '<div id="crp_related_post_' + post_id +'" data-post="' + post_id + '">' + crp_admin.remove_image_from + title + '</div>';
            jQuery('#crp_relations_single_from').prepend(element);
        } else if(from) {
            var title = btn.parents('tr').find('td').first().text();
            var element = '<div id="crp_related_post_' + post_id +'" data-post="' + post_id + '">' + title + crp_admin.remove_image_to + '</div>';
            jQuery('#crp_relations_single_to').prepend(element);
        }
    }
    jQuery('#crp_related_post_' + post_id).remove();

    var data = {
        action: 'crp_remove_relation',
        security: crp_admin.nonce,
        base: CustomRelatedPosts.post_id,
        target: post_id,
        from: both || from,
        to: both || to
    };

    jQuery.post(crp_admin.ajax_url, data);
};

CustomRelatedPosts.searchPosts = function() {
    jQuery('.crp_search_results').slideUp(250);
    CustomRelatedPosts.search_button.addClass('crp_spinner');

    var search_term = CustomRelatedPosts.search_term_field.val();

    if(search_term.length == 0) {
        CustomRelatedPosts.search_button.removeClass('crp_spinner');
        jQuery('#crp_search_results_input').slideDown(500);
        CustomRelatedPosts.search_term_field.focus();
    } else {
        var data = {
            action: 'crp_search_posts',
            security: crp_admin.nonce,
            term: search_term,
            base: CustomRelatedPosts.post_id
        };

        jQuery.post(crp_admin.ajax_url, data, function(posts) {
            CustomRelatedPosts.search_button.removeClass('crp_spinner');

            if(posts.length == 0) {
                jQuery('#crp_search_results_none').slideDown(500);
                CustomRelatedPosts.search_term_field.focus();
            } else {
                jQuery('#crp_search_results_table')
                    .slideDown(500)
                    .find('tbody').html(posts);
            }
        }, 'html');
    }
};

CustomRelatedPosts.linkTo = function(post_id) {
    CustomRelatedPosts.addLink(post_id, false, true);
    CustomRelatedPosts.disableActions(post_id, false);

    var title = jQuery('#crp_post_' + post_id + '_title').text();
    var element = '<div id="crp_related_post_' + post_id +'" data-post="' + post_id + '">' + title + crp_admin.remove_image_to + '</div>';
    jQuery('#crp_relations_single_to').append(element);
};
CustomRelatedPosts.linkFrom = function(post_id) {
    CustomRelatedPosts.addLink(post_id, true, false);
    CustomRelatedPosts.disableActions(post_id, false);

    var title = jQuery('#crp_post_' + post_id + '_title').text();
    var element = '<div id="crp_related_post_' + post_id +'" data-post="' + post_id + '">' + crp_admin.remove_image_from + title +'</div>';
    jQuery('#crp_relations_single_from').append(element);
};

CustomRelatedPosts.linkBoth = function(post_id) {
    CustomRelatedPosts.addLink(post_id, true, true);
    CustomRelatedPosts.disableActions(post_id, true);

    // Remove any links that might already exists
    jQuery('#crp_related_post_' + post_id).remove();

    var title = jQuery('#crp_post_' + post_id + '_title').text();
    var element = '<tr id="crp_related_post_' + post_id +'" data-post="' + post_id + '"><td>' + title + crp_admin.remove_image_to + '</td><td><div class="crp_link">' + crp_admin.remove_image_both + '</div></td><td>' + crp_admin.remove_image_from + title + '</td></tr>';
    jQuery('#crp_relations_both').append(element);
};

CustomRelatedPosts.disableActions = function(post_id, both) {
    jQuery('#crp_post_' + post_id + '_actions').find('button').each(function(index, elem) {
        console.log()
        if(both || index != 1) {
            elem.disabled = true;
        }
    });
};

CustomRelatedPosts.addLink = function(post_id, from, to) {
    var data = {
        action: 'crp_link_posts',
        security: crp_admin.nonce,
        base: CustomRelatedPosts.post_id,
        target: post_id,
        from: from,
        to: to
    };

    jQuery.post(crp_admin.ajax_url, data);
};

jQuery(document).ready(function($) {
    if($('#crp_meta_box').length > 0) {
        CustomRelatedPosts.initMetaBox();
    }
});