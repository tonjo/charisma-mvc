$(function() {

    $('.user-delete').click(function () {
        var userID = $(this).parentsUntil('tr').parent().attr('data-id');
        var user_name = $(this).parent().siblings('.user_name').html();
        var user_email = $(this).parent().siblings('.user_email').html();
        var itemname = user_name + ' (' +user_email+')';
        $('#itemname-delete').html(itemname);
        $('.itemtype-delete').html('utente');
        $form = $('#confirm_delete_item').find('form');
        var action = "admin/delete_user";
        var actionbase = $form.attr('data-actionbase');
        $form.each(function() {this.action = actionbase + action; });
        $form.children("input[name='deleteID']").val(userID);
        $('#confirm_delete_item').modal('show');
    });

    /*** MASSIVE MAIL ***/
    /*  good also for single mail */
    $('.massive_mail_btn').click(function() {
        $form = $('#confirm_send_mail').find('form');
        $('.mail_text').show();
        // $('#confirm_mail_text').show();
        // $('#confirm_mail_text').html();

        var userID = $(this).parentsUntil('tr').parent().attr('data-id');
        if (userID) {
            var user_name = $(this).parent().siblings('.user_name').html();
            var user_email = $(this).parent().siblings('.user_email').html();
            var itemname = user_name + ' (' +user_email+')';
            var itemname = '<b>'+user_name + '</b> (<span class="highlight">' +user_email+'</span>)';
            $("input[name='regenerateID']").val(userID);
            $('.mail_to_who').html(itemname);
            $('#userlist').hide();
        } else {
            $('.mail_to_who').html('<b>tutti gli utenti specificati</b>');
            $('#userlist').show();
        }
        var action = "admin/massive_mail";
        var actionbase = $form.attr('data-actionbase');
        $form.each(function() {this.action = actionbase + action; });
        $('#confirm_send_mail').modal('show');
    });

    $('#submit_mail').click(function (e) {
        e.preventDefault();
        $form = $('#confirm_send_mail').find('form');
        $form.find('button[type=reset]').attr('disabled',true);
        $('#confirm_send_mail').find('button.close').attr('disabled',true);
        $('.mail_text').hide();
        $('#confirm_mail_loading').show();
        $form.submit();
    });

});