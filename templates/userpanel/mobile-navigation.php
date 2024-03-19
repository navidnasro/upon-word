<?php

defined('ABSPATH') || exit;

?>

<div
class="mobile-panel-nav-bg hidden bg-black/[0.5] fixed top-0 bottom-0 right-0 left-0 w-full h-full z-10"
style="padding: 0;margin: 0;"></div>
<nav class="mobile-panel-nav md:hidden w-full bg-white -right-[500vh] absolute z-[11] flex flex-col items-center justify-start rounded-[15px] space-y-11 border grow border-solid border-[#EBEBEB] pt-10 pb-4 lg:pr-7">
    <div class="flex w-full flex-col items-center justify-start space-y-[17px]">
    <span class="w-[134px] h-[134px] rounded-full p-[13px] bg-[#00000008] flex items-center justify-center">
        <img class="rounded-full w-full" src="<?php echo $userAvatar ?>">
    </span>
        <span class="text-[20px] text-[#43454D] font-bold">
        <?php echo $user->display_name ?>
    </span>
    </div>
    <ul class="flex flex-col items-center justify-start w-full space-y-5">
        <?php foreach ( $items as $endpoint => $label ) : ?>
            <?php
            if (end($items) == $label)
            {
                echo '<div class="w-full pl-7"><hr class="w-full bg-[#DFDFDF] rounded-full"></div>';
            }
            ?>
            <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?> pr-9 py-2.5 w-full lg:rounded-r-lg relative">
                <a class="w-full flex items-center justify-start relative pr-9 text-[15px] text-[#43454D] font-bold"
                   href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>