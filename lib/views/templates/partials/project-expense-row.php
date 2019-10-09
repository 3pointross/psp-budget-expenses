<?php
global $post;

$data = array(
    'postid'       =>  $post->ID,
    'date'         =>  get_the_date('m/d/Y'),
    'desc'         =>  get_post_meta( $post->ID, '_psp-expense-description', true ),
    'categories'   =>  get_the_terms( $post->ID, 'psp_expenses' ),
    'cost'         =>  get_post_meta( $post->ID, '_psp-expense-cost', true )
);

$cost += intval($data['cost']); ?>

<tr class="psp-expense-row" <?php foreach( $data as $key => $val ) echo 'data-' . $key . '="' . esc_attr( $val ) . '" '; ?>>

    <td class="psp-expense-date-td"><?php echo esc_html($data['date']); ?></td>
    <td class="psp-expense-description-td"><?php echo esc_html($data['desc']); ?></td>
    <td class="psp-expense-categories-td">
        <?php
        $string = '';
        $cats   = get_the_terms( $post->ID, 'psp_expenses' );

        if( $cats ) {

            foreach( $cats as $cat ) $string .= '<span class="expense-cat" data-slug="' . $cat->slug . '">' . $cat->name . '</span>, ';

            $string = substr( $string, 0, -2 );

            echo $string;

        } ?>
    </td>
    <td class="psp-expense-cost-td"><?php echo esc_html_e( '$', 'psp_projects' ); ?><?php echo esc_html(number_format(intval($data['cost']))); ?></td>
    <td class="psp-expense-actions">
        <?php if( current_user_can('edit_psp_expenses') ): ?>
            <a href="#" class="js-psp-edit-expense"><i class="fa fa-pencil"></i></a>
            <a href="#" class="js-psp-del-expense"><i class="fa fa-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
