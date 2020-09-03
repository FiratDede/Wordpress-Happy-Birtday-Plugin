<?php
/*
    * Plugin name: Happy Birthday  Plugin
    *Description: Adds a birthdate section to an user's profile. If the date is the user's birthdate, celebrate the user's birthday in its profile
    * Author: Fırat Dede
    *License:     GPL2

*/




add_action( 'admin_enqueue_scripts', "fd_add_my_scripts", 10,1);

//Add jquery-ui script to profile.php and user-edit.php pages
function fd_add_my_scripts($hook_suffix){               
    if($hook_suffix!="profile.php"&& $hook_suffix!="user-edit.php") return;
     wp_enqueue_script( 'jquery-ui-datepicker' );    
     wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
     wp_enqueue_style( 'jquery-ui' ); 
     
}
add_action( "show_user_profile", "fd_show_user_profile_with_birthdate", 10,1 );
//Add birtday section to any user's profile 
function fd_show_user_profile_with_birthdate($profileuser ){
  ?>

<script>
jQuery(document).ready(function($){
   
   var fd_birthday_section_html= " <tr class='fd_birthday_section'><th >Birthday</th>"+ 
   "<td ><input type='text' readonly='readonly' id='fd_my_birthdate' placeholder='mm/dd/yyyy' name='fd_my_birthdate'"+
    "value='<?php echo get_user_meta( $profileuser->ID, "fd_user_birthdate", true )  ?>'>  </td></tr>";
    $(fd_birthday_section_html).insertAfter(".user-description-wrap"); 

     
    $( "#fd_my_birthdate" ).datepicker({
        dateFormat: 'mm-dd-yy',
        minDate: new Date('1-1-1879'),
        maxDate: 0,
        changeYear: true,
        yearRange: "1879:c"
    });
    $("#fd_my_birthdate").mouseover(function(){
        var a=this;
        a.style.cursor = "pointer";
    })
})
</script>
  <?php
  
    
}
add_action( "edit_user_profile", "fd_edit_user_profile_with_birthdate", 10,1);

//Add  edit facility for editing birtdays of users to edit-user.php
function fd_edit_user_profile_with_birthdate($profileuser){
    ?>
    <script>
jQuery(document).ready(function($){
   
   var fd_birthday_section_html= " <tr class='fd_birthday_section'><th >Birthday</th>"+ 
   "<td ><input type='text' readonly='readonly' id='fd_my_birthdate' placeholder='mm/dd/yyyy' name='fd_my_birthdate'"+
    "value='<?php echo get_user_meta( $profileuser->ID, "fd_user_birthdate", true )  ?>'>  </td></tr>";
    $(fd_birthday_section_html).insertAfter(".user-description-wrap"); 

     
    $( "#fd_my_birthdate" ).datepicker({
        dateFormat: 'mm-dd-yy',
        minDate: new Date('1-1-1879'),
        maxDate: 0,
        changeYear: true,
        yearRange: "1879:c"
    });
    $("#fd_my_birthdate").mouseover(function(){
        var a=this;
        a.style.cursor = "pointer";
    })
})
</script>

    <?php
}

add_action( 'profile_update', "fd_profile_update_", 10, 2);
// Update a user's birtday in database.
function fd_profile_update_($user_id,  $old_user_data)
{ 
    if(isset($_POST["fd_my_birthdate"])){
        update_user_meta( $user_id, "fd_user_birthdate",$_POST["fd_my_birthdate"] );
    }
  
}


add_action( 'wp_dashboard_setup', "fd_show_happy_birthday_title",10,0);

//If a user's birtday comes, it shows an happy birtday writing for the user.

function fd_show_happy_birthday_title(){               
   $user_birth_date= get_user_meta( get_current_user_id(), "fd_user_birthdate", true );
   if(empty($user_birth_date)) return;
   $user_birth_date = explode('-', $user_birth_date);
   $month=$user_birth_date[0];
   $day=$user_birth_date[1];
   $current_date=current_time( "m-d-y", true );
   $current_date=explode("-",$current_date);
   $current_month=$current_date[0];
   $current_day=$current_date[1];
   if($month!=$current_month||$day!=$current_day) return;
   
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <style>
    .fd_happy_birthday_title {
    
    font-size: 14px;
    float: left;
    margin-top: 5px;
   
    }
    </style>
   
    <script>
jQuery(document).ready(function($){
      $("<div class='fd_happy_birthday_title'>Happy Birthday <?php echo get_user_by("id",get_current_user_id() )->user_login;  ?> </div>").insertBefore("#screen-options-link-wrap");

    });
    </script>
    <?php
}
register_deactivation_hook( __FILE__, "fd_delete_user_meta_for_this_plugin");

//If the plugin is deactivated, it deletes all data which is releated to happy birtday information of users.
  
function fd_delete_user_meta_for_this_plugin(){
    $all_users=get_users( ["fields"=>"ID","meta_key"=>"fd_user_birthdate"] );
    foreach ($all_users as $user_id){
        delete_user_meta( $user_id, "fd_user_birthdate");
    }

}
