<script>
    [{
        id: 'wmd-undo-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="20" height="20"><path d="M76 463.7l294.8 294.9c19.5 19.4 52.8 5.6 52.8-21.9V561.5c202.5-8.2 344.1 59.5 501.6 338.3 8.5 15 31.5 7.9 30.6-9.3-30.5-554.7-453-571.4-532.3-569.6v-174c0-27.5-33.2-41.3-52.7-21.8L75.9 420c-12 12.1-12 31.6.1 43.7z"></path></svg>',
        insertBefore: '#wmd-bold-button'
    }, {
        id: 'wmd-redo-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="20" height="20"><path d="M946.8 420L651.9 125.1c-19.5-19.5-52.7-5.7-52.7 21.8v174c-79.3-1.8-501.8 14.9-532.3 569.6-.9 17.2 22.1 24.3 30.6 9.3C255 621 396.6 553.3 599.1 561.5v175.2c0 27.5 33.3 41.3 52.8 21.9l294.8-294.9c12.1-12.1 12.1-31.6.1-43.7z"></path></svg>',
        insertBefore: '#wmd-bold-button'
    }, {
        insertBefore: '#wmd-bold-button'
    }, {
        id: 'wmd-bold-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"width="20" height="20"><path d="M341.333 469.333h192a106.667 106.667 0 1 0 0-213.333h-192v213.333zm426.667 192a192 192 0 0 1-192 192H256V170.667h277.333a192 192 0 0 1 138.923 324.522A191.915 191.915 0 0 1 768 661.333zM341.333 554.667V768H576a106.667 106.667 0 1 0 0-213.333H341.333z"></path></svg>',
    }, {
        id: 'wmd-strikethrough-button',
        name: '<?php use TypechoPlugin\AAEditor\Util;_e("删除线 <del>"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="20" height="20"><path d="M731.904 597.333c9.813 22.016 14.763 46.507 14.763 73.387 0 57.259-22.358 102.059-67.03 134.272-44.757 32.213-106.496 48.341-185.301 48.341-69.973 0-139.221-16.256-207.787-48.81v-96.256c64.854 37.418 131.2 56.149 199.083 56.149 108.843 0 163.413-31.232 163.797-93.739a94.293 94.293 0 0 0-27.648-68.394l-5.12-4.992H128v-85.334h768v85.334H731.904zm-173.995-128H325.504a174.336 174.336 0 0 1-20.523-22.272c-18.432-23.808-27.648-52.565-27.648-86.442 0-52.736 19.883-97.579 59.606-134.528 39.808-36.95 101.29-55.424 184.533-55.424 62.763 0 122.837 13.994 180.139 41.984v91.818c-51.2-29.312-107.307-43.946-168.363-43.946-105.813 0-158.677 33.365-158.677 100.096 0 17.92 9.301 33.536 27.904 46.89 18.602 13.355 41.557 23.979 68.821 32 26.453 7.68 55.339 17.664 86.613 29.824z"></path></svg>',
        insertAfter: '#wmd-bold-button',
        shortcut: 'ctrl+d',
        command: function () {
            this.wrapText('~~', '~~', '<?php _e("删除线"); ?>');
        }
    }, {
        id: 'wmd-italic-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="20" height="20"><path d="M640 853.333H298.667V768h124.885l90.283-512H384v-85.333h341.333V256H600.448l-90.283 512H640z"></path></svg>'
    }, {
        id: 'wmd-quote-button',
        remove: true
    }, {
        id: 'wmd-quote-button',
        name: '<?php _e("引用文字"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M7.984375 5L3 11.697266L3 12.027344L3 14.027344L3 17.027344C3 18.130344 3.897 19.027344 5 19.027344L9 19.027344C10.103 19.027344 11 18.130344 11 17.027344L11 13.027344C11 11.925344 10.103 11.027344 9 11.027344L5.9902344 11.027344L10.478516 5L7.984375 5 z M 17.964844 5L13 11.669922L13 12L13 14L13 17C13 18.103 13.897 19 15 19L19 19C20.103 19 21 18.103 21 17L21 13C21 11.897 20.103 11 19 11L15.990234 11L20.457031 5L17.964844 5 z"></path></svg>',
        insertAfter: '#wmd-italic-button',
        shortcut: 'ctrl+q',
        command: function () {
            this.blockPrefix("> ", "<?php _e("引用文字"); ?>");
        }
    }, {
        id: 'wmd-heading-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M5 3L5 21L8 21L8 13L16 13L16 21L19 21L19 3L16 3L16 10L8 10L8 3L5 3 z"/></svg>',
        insertAfter: '#wmd-quote-button',
    }, {
        id: 'wmd-heading1-button',
        name: '<?php _e("标题1 <h1>"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="20" height="20"><path d="M584.9 445.3V142.6h86.5v735h-86.5V531.7H152.5v345.9H66.1v-735h86.5v302.6l432.3 0.1zM843.7 877.6v-0.3h-79.8V826h79.8V558.6c-22.5 23.4-49.9 40.4-79.8 49.3v-59.1c17.2-5.2 33.8-12.9 49.1-23 16.2-10.6 30.9-23.7 43.9-38.9h39.9V826h61.7v51.3h-61.7v0.3h-53.1z"></path></svg>',
        insertBefore: '#wmd-spacer1',
        shortcut: 'ctrl+alt+1',
        command() {
            this.firstSelectionLinePrefix("# ", "<?php _e("标题1"); ?>");
        }
    }, {
        id: 'wmd-heading2-button',
        name: '<?php _e("标题2 <h2>"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M88 448h400V172c0-24.3 19.7-44 44-44s44 19.7 44 44v680c0 24.3-19.7 44-44 44s-44-19.7-44-44V536H88v316c0 24.3-19.7 44-44 44S0 876.3 0 852V172c0-24.3 19.7-44 44-44s44 19.7 44 44v276z m935.282 448H680c0.479-41.591 10.533-77.923 30.163-108.997 19.63-31.074 46.44-58.084 80.434-81.031 16.279-11.952 33.275-23.544 50.99-34.779 17.714-11.234 33.993-23.305 48.835-36.213 14.842-12.907 27.05-26.89 36.626-41.95 9.576-15.058 14.603-32.388 15.081-51.988 0-9.083-1.077-18.764-3.231-29.042-2.155-10.278-6.344-19.84-12.568-28.683-6.224-8.845-14.842-16.254-25.854-22.23-11.012-5.976-25.375-8.964-43.09-8.964-16.278 0-29.803 3.227-40.576 9.68-10.772 6.455-19.39 15.299-25.854 26.533-6.463 11.235-11.251 24.5-14.363 39.798-3.112 15.298-4.908 31.791-5.386 49.48h-81.87c0-27.728 3.71-53.423 11.13-77.087 7.422-23.664 18.553-44.101 33.395-61.311 14.842-17.21 32.916-30.715 54.222-40.516 21.305-9.8 46.081-14.7 74.33-14.7 30.641 0 56.255 5.02 76.843 15.059 20.587 10.04 37.224 22.707 49.912 38.005 12.688 15.298 21.665 31.91 26.931 49.838 5.267 17.927 7.9 35.018 7.9 51.272 0 20.078-3.112 38.244-9.336 54.498-6.224 16.254-14.603 31.193-25.136 44.818-10.533 13.625-22.502 26.174-35.908 37.647a538.302 538.302 0 0 0-41.653 32.27 1122.27 1122.27 0 0 0-43.09 28.683c-14.364 9.083-27.65 18.166-39.858 27.249-12.209 9.083-22.862 18.525-31.958 28.325-9.097 9.8-15.321 20.198-18.673 31.193h244.894V896z"></path></svg>',
        insertBefore: '#wmd-spacer1',
        shortcut: 'ctrl+alt+2',
        command() {
            this.firstSelectionLinePrefix("## ", "<?php _e("标题2"); ?>");
        }
    }, {
        id: 'wmd-heading3-button',
        name: '<?php _e("标题3 <h3>"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M88 448h400V172c0-24.3 19.7-44 44-44s44 19.7 44 44v680c0 24.3-19.7 44-44 44s-44-19.7-44-44V536H88v316c0 24.3-19.7 44-44 44S0 876.3 0 852V172c0-24.3 19.7-44 44-44s44 19.7 44 44v276zM815.551 597.802c13.128 0.47 26.257-0.469 39.385-2.813 13.129-2.344 24.85-6.447 35.165-12.308 10.316-5.86 18.638-13.948 24.968-24.263 6.33-10.315 9.494-22.975 9.494-37.978 0-21.1-7.15-37.978-21.45-50.638-14.301-12.66-32.704-18.989-55.21-18.989-14.066 0-26.257 2.813-36.572 8.44-10.315 5.626-18.872 13.245-25.67 22.857-6.799 9.612-11.84 20.395-15.121 32.352-3.283 11.956-4.69 24.263-4.22 36.923h-80.177c0.938-23.913 5.392-46.066 13.363-66.462 7.97-20.396 18.872-38.095 32.703-53.099 13.832-15.004 30.594-26.725 50.287-35.165C802.188 388.22 824.459 384 849.31 384c19.223 0 38.095 2.813 56.616 8.44 18.52 5.626 35.165 13.831 49.934 24.615 14.77 10.784 26.609 24.498 35.517 41.143 8.909 16.645 13.363 35.75 13.363 57.318 0 24.85-5.626 46.535-16.88 65.055-11.252 18.52-28.835 32-52.747 40.44v1.407c28.132 5.626 50.052 19.575 65.759 41.846 15.707 22.27 23.56 49.348 23.56 81.23 0 23.444-4.688 44.425-14.065 62.946-9.378 18.52-22.037 34.227-37.979 47.12-15.942 12.894-34.462 22.858-55.561 29.89-21.1 7.034-43.37 10.55-66.814 10.55-28.601 0-53.568-4.103-74.902-12.308-21.334-8.205-39.15-19.81-53.451-34.813-14.3-15.004-25.202-33.055-32.704-54.154-7.502-21.099-11.487-44.542-11.956-70.33h80.177c-0.938 30.008 6.447 54.975 22.154 74.902s39.268 29.89 70.682 29.89c26.726 0 49.114-7.62 67.166-22.857 18.051-15.239 27.077-36.923 27.077-65.055 0-19.224-3.751-34.462-11.253-45.715-7.502-11.252-17.348-19.81-29.539-25.67-12.19-5.86-25.905-9.494-41.143-10.901-15.239-1.407-30.828-1.875-46.77-1.407v-59.78z"></path></svg>',
        insertBefore: '#wmd-spacer1',
        shortcut: 'ctrl+alt+3',
        command() {
            this.firstSelectionLinePrefix("### ", "<?php _e("标题3"); ?>");
        }
    }, {
        id: 'wmd-heading4-button',
        name: '<?php _e("标题4 <h4>"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M88 448h400V172c0-24.3 19.7-44 44-44s44 19.7 44 44v680c0 24.3-19.7 44-44 44s-44-19.7-44-44V536H88v316c0 24.3-19.7 44-44 44S0 876.3 0 852V172c0-24.3 19.7-44 44-44s44 19.7 44 44v276z m936.246 331.56h-63.298v116.748h-75.957V779.56H674v-79.472L884.991 404h75.957v312.264h63.298v63.296zM735.89 716.264h149.1V499.648h-1.406L735.89 716.264z"></path></svg>',
        insertBefore: '#wmd-spacer1',
        shortcut: 'ctrl+alt+4',
        command() {
            this.firstSelectionLinePrefix("#### ", "<?php _e("标题4"); ?>");
        }
    }, {
        id: 'wmd-heading5-button',
        name: '<?php _e("标题5 <h1>"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M742.89 394h254.597v71.736H802.672l-25.32 125.187 1.407 1.407c10.784-12.19 24.499-21.45 41.144-27.78 16.645-6.33 33.172-9.495 49.583-9.495 24.381 0 46.183 4.102 65.407 12.308 19.224 8.205 35.4 19.692 48.528 34.461 13.129 14.77 23.092 32.235 29.89 52.396 6.8 20.161 10.198 41.963 10.198 65.406 0 19.693-3.164 39.971-9.494 60.836-6.33 20.864-16.41 39.853-30.242 56.967-13.832 17.113-31.532 31.179-53.1 42.197-21.568 11.019-47.355 16.528-77.363 16.528-23.913 0-46.067-3.165-66.463-9.495-20.396-6.33-38.33-15.824-53.802-28.483-15.473-12.66-27.78-28.25-36.924-46.77-9.143-18.52-14.183-40.204-15.121-65.054h80.177c2.344 26.725 11.487 47.238 27.429 61.538 15.941 14.3 37.04 21.45 63.297 21.45 16.88 0 31.18-2.812 42.902-8.439 11.721-5.626 21.216-13.362 28.484-23.209 7.267-9.846 12.425-21.333 15.472-34.461 3.048-13.129 4.572-27.194 4.572-42.198 0-13.597-1.876-26.608-5.627-39.033-3.75-12.425-9.377-23.326-16.879-32.703-7.502-9.378-17.231-16.88-29.187-22.506-11.956-5.626-25.905-8.44-41.847-8.44-16.88 0-32.703 3.165-47.473 9.495-14.77 6.33-25.436 18.169-32 35.517h-80.177L742.891 394zM88 448h400V172c0-24.3 19.7-44 44-44s44 19.7 44 44v680c0 24.3-19.7 44-44 44s-44-19.7-44-44V536H88v316c0 24.3-19.7 44-44 44S0 876.3 0 852V172c0-24.3 19.7-44 44-44s44 19.7 44 44v276z"></path></svg>',
        insertBefore: '#wmd-spacer1',
        shortcut: 'ctrl+alt+5',
        command() {
            this.firstSelectionLinePrefix("##### ", "<?php _e("标题5"); ?>");
        }
    }, {
        id: 'wmd-heading6-button',
        name: '<?php _e("标题1 <h1>"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M88 448h400V172c0-24.3 19.7-44 44-44s44 19.7 44 44v680c0 24.3-19.7 44-44 44s-44-19.7-44-44V536H88v316c0 24.3-19.7 44-44 44S0 876.3 0 852V172c0-24.3 19.7-44 44-44s44 19.7 44 44v276z m846.736 70.33c-1.875-20.162-9.02-36.454-21.438-48.88-12.417-12.424-29.169-18.637-50.255-18.637-14.526 0-27.06 2.696-37.603 8.088-10.544 5.392-19.446 12.542-26.71 21.45-7.262 8.91-13.12 19.107-17.571 30.594-4.452 11.487-8.083 23.56-10.895 36.22-2.811 12.66-4.803 25.201-5.974 37.626a4794.582 4794.582 0 0 1-3.163 34.813l1.406 1.407c10.777-19.692 25.654-34.344 44.632-43.956 18.978-9.612 39.478-14.418 61.501-14.418 24.366 0 46.155 4.22 65.367 12.66 19.212 8.44 35.495 20.044 48.85 34.813 13.354 14.77 23.545 32.234 30.574 52.395 7.029 20.162 10.543 41.964 10.543 65.407 0 23.912-3.866 46.066-11.597 66.462-7.732 20.395-18.86 38.212-33.387 53.45-14.526 15.238-31.863 27.077-52.012 35.517C906.855 891.78 884.598 896 860.232 896c-36.081 0-65.719-6.681-88.913-20.044-23.195-13.363-41.47-31.648-54.824-54.857-13.355-23.209-22.609-50.403-27.763-81.583-5.155-31.18-7.732-64.82-7.732-100.923 0-29.538 3.163-59.31 9.489-89.318 6.326-30.008 16.751-57.319 31.277-81.934 14.526-24.616 33.62-44.66 57.284-60.132C802.714 391.736 831.882 384 866.557 384c19.68 0 38.19 3.282 55.527 9.846 17.338 6.564 32.683 15.707 46.038 27.429 13.355 11.721 24.249 25.787 32.683 42.198 8.435 16.41 13.12 34.695 14.058 54.857h-80.127zM857.42 829.187c14.526 0 27.178-2.93 37.955-8.791 10.777-5.861 19.797-13.48 27.06-22.858 7.263-9.377 12.652-20.278 16.166-32.703 3.515-12.425 5.272-25.201 5.272-38.33 0-13.128-1.757-25.787-5.272-37.978-3.514-12.19-8.903-22.857-16.166-32-7.263-9.142-16.283-16.527-27.06-22.153-10.777-5.627-23.429-8.44-37.955-8.44s-27.295 2.696-38.306 8.088c-11.012 5.392-20.266 12.66-27.764 21.802-7.497 9.143-13.12 19.81-16.868 32-3.75 12.19-5.623 25.084-5.623 38.681 0 13.598 1.874 26.491 5.623 38.682 3.748 12.19 9.371 23.091 16.868 32.703 7.498 9.612 16.752 17.23 27.764 22.857 11.011 5.627 23.78 8.44 38.306 8.44z"></path></svg>',
        insertBefore: '#wmd-spacer1',
        shortcut: 'ctrl+alt+6',
        command() {
            this.firstSelectionLinePrefix("###### ", "<?php _e("标题6"); ?>");
        }
    }, {
        id: 'wmd-center-button',
        name: "<?php _e("居中 <center>") ?>",
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M11 4V2H13V4H19C19.5523 4 20 4.44772 20 5V10C20 10.5523 19.5523 11 19 11H13V13H17C17.5523 13 18 13.4477 18 14V19C18 19.5523 17.5523 20 17 20H13V22H11V20H7C6.44772 20 6 19.5523 6 19V14C6 13.4477 6.44772 13 7 13H11V11H5C4.44772 11 4 10.5523 4 10V5C4 4.44772 4.44772 4 5 4H11ZM8 15V18H16V15H8ZM6 9H18V6H6V9Z"></path></svg>',
        insertBefore: '#wmd-spacer1',
        command() {
            this.wrapText(`<center>`, `</center>`);
        }
    }, {
        id: 'wmd-link-button',
        remove: true
    }, {
        id: 'wmd-link-button',
        name: '<?php _e("链接 <a>"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M13.0605 8.11073L14.4747 9.52494C17.2084 12.2586 17.2084 16.6908 14.4747 19.4244L14.1211 19.778C11.3875 22.5117 6.95531 22.5117 4.22164 19.778C1.48797 17.0443 1.48797 12.6122 4.22164 9.87849L5.63585 11.2927C3.68323 13.2453 3.68323 16.4112 5.63585 18.3638C7.58847 20.3164 10.7543 20.3164 12.7069 18.3638L13.0605 18.0102C15.0131 16.0576 15.0131 12.8918 13.0605 10.9392L11.6463 9.52494L13.0605 8.11073ZM19.778 14.1211L18.3638 12.7069C20.3164 10.7543 20.3164 7.58847 18.3638 5.63585C16.4112 3.68323 13.2453 3.68323 11.2927 5.63585L10.9392 5.98941C8.98653 7.94203 8.98653 11.1079 10.9392 13.0605L12.3534 14.4747L10.9392 15.8889L9.52494 14.4747C6.79127 11.741 6.79127 7.30886 9.52494 4.57519L9.87849 4.22164C12.6122 1.48797 17.0443 1.48797 19.778 4.22164C22.5117 6.95531 22.5117 11.3875 19.778 14.1211Z"></path></svg>',
        shortcut: 'ctrl+l',
        insertBefore: '#wmd-spacer2',
        command() {
            const markdownReg = /\[([^\]]+)\]\(([^)]+)\)/ig; // Markdown 链接正则
            const httpLinkReg = /https?:\/\/\S+/ig; // HTTP链接正则
            const {textarea} = this;
            let lastSelection = textarea.getSelection();
            let selectedText = textarea.getSelectedText(),
                title = '',
                url = '';

            if (selectedText) {
                if (textarea.getTextInRange(lastSelection.start - 1, lastSelection.start) === "!") {
                    textarea.setSelection(lastSelection.start - 1, lastSelection.end);
                    document.getElementById('wmd-image-button').click();
                    return;
                }
                markdownReg.lastIndex = 0;
                let m;
                if ((m = markdownReg.exec(selectedText)) !== null) {
                    title = m[1];
                    url = m[2];
                } else {
                    const httpMatch = selectedText.match(httpLinkReg);
                    if (httpMatch && httpMatch.length > 0) {
                        url = httpMatch[0];
                    } else {
                        title = selectedText;
                    }
                }
            }

            this.openModal({
                title: '<?php _e("插入链接"); ?>',
                innerHTML: `
                    <div class="form-item">
                        <label><?php _e("标题"); ?></label>
                        <input type="text" name="title" placeholder="<?php _e("请输入标题"); ?>" value="${title}" />
                    </div>
                    <div class="form-item">
                        <label class="required"><?php _e("链接"); ?></label>
                        <input required type="text" name="url" placeholder="<?php _e("请输入链接"); ?>" value="${url}" />
                    </div>
                `,
                confirm(modal) {
                    let url = $('[name="url"]', modal).val();
                    let title = $('[name="title"]', modal).val() || url;
                    this.textarea.setSelection(lastSelection.start, lastSelection.end);
                    this.textarea.executeAndAddUndoStack('replaceSelectionText', `[${title}](${url})`);
                    lastSelection = this.textarea.getSelection();
                    this.textarea.setSelection(lastSelection.end, lastSelection.end);
                    return true;
                }
            });
        }
    }, {
        id: 'wmd-image-button',
        remove: true
    }, {
        id: 'wmd-image-button',
        name: '<?php _e("图片"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M288 480c54.4 0 96-41.6 96-96s-41.6-96-96-96-96 41.6-96 96 41.6 96 96 96zm0-128c19.2 0 32 12.8 32 32s-12.8 32-32 32-32-12.8-32-32 12.8-32 32-32z"></path><path d="M864 160H160c-54.4 0-96 41.6-96 96v512c0 54.4 41.6 96 96 96h704c54.4 0 96-41.6 96-96V256c0-54.4-41.6-96-96-96zM128 768V256c0-19.2 12.8-32 32-32h704c19.2 0 32 12.8 32 32v364.8L726.4 451.2c-38.4-38.4-99.2-38.4-134.4 0L243.2 800H160c-19.2 0-32-12.8-32-32zm736 32H332.8l304-304c12.8-12.8 32-12.8 44.8 0L896 710.4V768c0 19.2-12.8 32-32 32z"></path></svg>',
        shortcut: 'ctrl+g',
        insertBefore: '#wmd-spacer2',
        command() {
            const markdownReg = /!\[([^\]]*?)]\(([^"]*?)(?: "([^"]*?)")?\)/ig;// Markdown 图片正则
            const {textarea} = this;
            let lastSelection = textarea.getSelection();
            let selectedText = textarea.getSelectedText(),
                alt = '', // 添加 alt 变量
                title = '',
                url = '';

            if (selectedText) {
                if (!selectedText.startsWith("!") && textarea.getTextInRange(lastSelection.start - 1, lastSelection.start) === "!") {
                    textarea.setSelection(lastSelection.start - 1, lastSelection.end);
                    lastSelection = textarea.getSelection();
                    selectedText = textarea.getSelectedText();
                }
                markdownReg.lastIndex = 0;
                let m;
                if ((m = markdownReg.exec(selectedText)) !== null) {
                    alt = m[1]; // 获取 alt 信息
                    url = m[2];
                    title = m[3] || '';
                }
            }

            this.openModal({
                title: '<?php _e("插入图片"); ?>',
                innerHTML: `
            <div class="form-item">
                <label><?php _e("图片标题"); ?></label>
                <input type="text" name="title" placeholder="<?php _e("请输入标题"); ?>" value="${title}" />
            </div>
            <div class="form-item">
                <label><?php _e("替代文字"); ?></label>
                <input type="text" name="alt" placeholder="<?php _e("图片无法显示时的代替文字"); ?>" value="${alt}" />
            </div>
            <div class="form-item">
                <label class="required"><?php _e("图片链接"); ?></label>
                <input required type="text" name="url" placeholder="<?php _e("请输入链接"); ?>" value="${url}" />
            </div>
        `,
                confirm(modal) {
                    let alt = $('[name="alt"]', modal).val() || ""; // 获取 alt 文本
                    let url = $('[name="url"]', modal).val();
                    let title = $('[name="title"]', modal).val() || "";
                    this.textarea.setSelection(lastSelection.start, lastSelection.end);
                    if (title) {
                        this.textarea.executeAndAddUndoStack('replaceSelectionText', `![${alt}](${url} "${title}")`); // 有 title 时的插入格式
                    } else {
                        this.textarea.executeAndAddUndoStack('replaceSelectionText', `![${alt}](${url})`); // 没有 title 时的插入格式
                    }
                    lastSelection = this.textarea.getSelection();
                    this.textarea.setSelection(lastSelection.end, lastSelection.end);
                    return true;
                }
            });
        }
    }, {
        id: 'wmd-code-button',
        remove: true
    }, {
        id: 'wmd-inline-code-button',
        name: '<?php _e("行内代码"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M24 12L18.3431 17.6569L16.9289 16.2426L21.1716 12L16.9289 7.75736L18.3431 6.34315L24 12ZM2.82843 12L7.07107 16.2426L5.65685 17.6569L0 12L5.65685 6.34315L7.07107 7.75736L2.82843 12ZM9.78845 21H7.66009L14.2116 3H16.3399L9.78845 21Z"></path></svg>',
        insertBefore: '#wmd-spacer2',
        command() {
            this.wrapText('`', '`');
        }
    }, {
        id: 'wmd-block-code-button',
        name: '<?php _e("块级代码"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M3 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3ZM4 5V19H20V5H4ZM20 12L16.4645 15.5355L15.0503 14.1213L17.1716 12L15.0503 9.87868L16.4645 8.46447L20 12ZM6.82843 12L8.94975 14.1213L7.53553 15.5355L4 12L7.53553 8.46447L8.94975 9.87868L6.82843 12ZM11.2443 17H9.11597L12.7557 7H14.884L11.2443 17Z"></path></svg>',
        insertBefore: '#wmd-spacer2',
        command() {
            this.wrapText('```\n', '\n```');
        }
    }, {
        id: 'wmd-html-icon',
        name: '<?php _e("HTML代码") ?>',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 18.1778L7.38083 16.9222L7.0517 13.3778H9.32156L9.48045 15.2222L12 15.8889L14.5195 15.2222L14.7806 12.3556H6.96091L6.32535 5.67778H17.6747L17.4477 7.88889H8.82219L9.02648 10.1444H17.2434L16.6192 16.9222L12 18.1778ZM3 2H21L19.377 20L12 22L4.62295 20L3 2ZM5.18844 4L6.48986 18.4339L12 19.9278L17.5101 18.4339L18.8116 4H5.18844Z"></path></svg>`,
        insertAfter: '#wmd-block-code-button',
        command() {
            this.replaceSelection((this.textarea.isAtLineStart() ? '' : '\n') + '!!!\n<?php _e("这里输入 HTML 代码"); ?>\n!!!' + (this.textarea.isAtLineEnd() ? '' : '\n'));
        }
    }, {
        id: 'wmd-table-button',
        name: "<?php _e("表格 <table>") ?>",
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="20" height="20"><path d="m1.96877,4.30731l0,23.38538l28.06246,0l0,-23.38538l-28.06246,0zm2.33854,2.33854l5.84635,0l0,4.67708l-5.84635,0l0,-4.67708zm8.18488,0l15.2005,0l0,4.67708l-15.2005,0l0,-4.67708zm-8.18488,7.01561l5.84635,0l0,4.67708l-5.84635,0l0,-4.67708zm8.18488,0l15.2005,0l0,4.67708l-15.2005,0l0,-4.67708zm-8.18488,7.01561l5.84635,0l0,4.67708l-5.84635,0l0,-4.67708zm8.18488,0l15.2005,0l0,4.67708l-15.2005,0l0,-4.67708z"></path></svg>',
        insertAfter: "#wmd-spacer2",
        command() {
            this.openModal({
                title: "<?php _e("插入表格") ?>",
                innerHTML: `<div class="form-item">
<div class="columns-2">
    <div class="column">
        <label for="rows"><?php _e("行数") ?></label>
        <input type="number" name="rows" min="1" value="3" required>
    </div>
    <div class="column">
        <label for="cols"><?php _e("列数") ?></label>
        <input type="number" name="cols" min="1" value="3" required>
    </div>
</div>
</div>
<div class="form-item">
    <label class="no-bg" for="is-center"><?php _e("居中") ?></label>
    <input type="checkbox" name="is-center" checked>
</div>`,
                confirm(modal) {
                    let rows = parseInt(modal.querySelector("input[name=rows]").value, 10);
                    if (rows < 1) rows = 1;
                    let cols = parseInt(modal.querySelector("input[name=cols]").value, 10);
                    if (cols < 1) cols = 1;
                    let isCenter = modal.querySelector("input[name=is-center]").checked;
                    let th = "", split = "", td = "";
                    for (let i = 0; i < cols; i++) {
                        th += "| <?php _e("表头") ?> ";
                        split += isCenter ? '| :---: ' : '| ---- ';
                        td += "|      ";
                    }
                    th += "|\n";
                    split += "|\n";
                    td += "|\n";
                    td = td.repeat(rows);
                    if (!this.textarea.isAtLineStart()) {
                        th = "\n" + th;
                    }
                    if (this.textarea.isAtLineEnd()) {
                        td = td.slice(0, -1);
                    }
                    this.replaceSelection(th + split + td)
                    return true;
                }
            })
        }
    }, {
        id: 'wmd-olist-button',
        remove: true,
    }, {
        id: 'wmd-olist-button',
        name: '<?php _e("数字列表 <ol>"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M341.333 170.667H896V256H341.333v-85.333zm-128-42.667v128H256v42.667H128V256h42.667v-85.333H128V128h85.333zM128 597.333V490.667h85.333v-21.334H128v-42.666h128v106.666h-85.333v21.334H256v42.666H128zM213.333 832H128v-42.667h85.333V768H128v-42.667h128V896H128v-42.667h85.333V832zm128-362.667H896v85.334H341.333v-85.334zm0 298.667H896v85.333H341.333V768z"></path></svg>',
        insertBefore: '#wmd-hr-button',
        command: function () {
            this.blockPrefix("%n. ");
        }
    }, {
        id: 'wmd-ulist-button',
        remove: true,
    }, {
        id: 'wmd-ulist-button',
        name: '<?php _e("无序列表 <ol>"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M132.608 204.288m-66.56 0a66.56 66.56 0 1 0 133.12 0 66.56 66.56 0 1 0-133.12 0Z"></path><path d="M962.01728 158.80192l-680.68352 0.3584-0.04096 84.44928 680.7552-0.3584-0.03072-84.44928z"></path><path d="M132.608 512m-66.56 0a66.56 66.56 0 1 0 133.12 0 66.56 66.56 0 1 0-133.12 0Z"></path><path d="M281.33376 466.87232l-0.04096 84.44928 680.7552-0.3584-0.03072-84.44928-680.68352 0.3584z"></path><path d="M132.608 819.712m-66.56 0a66.56 66.56 0 1 0 133.12 0 66.56 66.56 0 1 0-133.12 0Z"></path><path d="M281.33376 775.59808l-0.04096 84.44928 680.7552-0.3584-0.03072-84.44928-680.68352 0.3584z"></path></svg>',
        insertBefore: '#wmd-hr-button',
        command: function () {
            this.blockPrefix("- ");
        }
    }, {
        id: 'wmd-task-button',
        name: '<?php _e("任务 - 未完成"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M831.55 128.531c38.35 0 63.911 25.568 63.911 63.91v639.104c0 38.355-25.561 63.916-63.91 63.916H192.44c-38.34 0-63.908-25.56-63.908-63.916V192.442c0-38.343 25.567-63.91 63.908-63.91h639.11m0-63.91H192.44c-70.3 0-127.816 57.518-127.816 127.82v639.103c0 70.308 57.515 127.833 127.816 127.833h639.11c70.294 0 127.822-57.525 127.822-127.833V192.442c0-70.302-57.527-127.82-127.823-127.82zm0 0"></path></svg>',
        insertBefore: '#wmd-hr-button',
        command: function () {
            this.blockPrefix("[ ] ", "<?php _e("任务 - 未完成"); ?>");
        }
    }, {
        id: 'wmd-task-checked-button',
        name: '<?php _e("任务 - 已完成"); ?>',
        icon: '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path d="M831.551 64.623h-639.11c-70.3 0-127.816 57.517-127.816 127.819v639.103c0 70.308 57.515 127.833 127.816 127.833h639.11c70.294 0 127.822-57.525 127.822-127.833V192.442c0-70.302-57.527-127.82-127.822-127.82zM646.217 486.44c-108.652 159.779-204.52 345.115-204.52 345.115L192.443 550.351l63.916-70.303 153.385 146.994s76.695-127.822 178.95-236.469c102.261-108.652 223.689-198.127 223.689-198.127l19.17 63.916c0-.001-102.255 108.646-185.337 230.078z"></path></svg>',
        insertBefore: '#wmd-hr-button',
        command: function () {
            this.blockPrefix("[x] ", "<?php _e("任务 - 已完成"); ?>");
        }
    }, {
        id: 'wmd-hr-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M2 11H4V13H2V11ZM6 11H18V13H6V11ZM20 11H22V13H20V11Z"></path></svg>'
    }, {
        id: 'wmd-more-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M6 10 A 2 2 0 0 0 4 12 A 2 2 0 0 0 6 14 A 2 2 0 0 0 8 12 A 2 2 0 0 0 6 10 z M 12 10 A 2 2 0 0 0 10 12 A 2 2 0 0 0 12 14 A 2 2 0 0 0 14 12 A 2 2 0 0 0 12 10 z M 18 10 A 2 2 0 0 0 16 12 A 2 2 0 0 0 18 14 A 2 2 0 0 0 20 12 A 2 2 0 0 0 18 10 z"/></svg>'
    }, {
        id: 'wmd-hide-button',
        name: '<?php _e("回复可见"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><path d="M23.986328 9C12.666705 9 2.6928719 16.845918 0.046875 27.126953 A 1.5002454 1.5002454 0 0 0 2.953125 27.873047C5.2331281 19.014082 14.065951 12 23.986328 12C33.906705 12 42.767507 19.01655 45.046875 27.873047 A 1.5002454 1.5002454 0 0 0 47.953125 27.126953C45.306493 16.84345 35.305951 9 23.986328 9 z M 24.001953 17C18.681885 17 14.337891 21.343999 14.337891 26.664062C14.337891 31.984127 18.681885 36.330078 24.001953 36.330078C29.322021 36.330078 33.667969 31.984126 33.667969 26.664062C33.667969 21.343999 29.322021 17 24.001953 17 z"></path></svg>',
        insertBefore: '#wmd-spacer4',
        command() {
            this.wrapText('[hide]\n', '\n[/hide]', '<?php _e("回复可见"); ?>');
        }
    }, {
        id: 'wmd-preview-button',
        name: '<?php _e("显示/隐藏预览窗格"); ?>',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M5 3C3.9069372 3 3 3.9069372 3 5L3 19C3 20.093063 3.9069372 21 5 21L19 21C20.093063 21 21 20.093063 21 19L21 5C21 3.9069372 20.093063 3 19 3L5 3 z M 5 5L19 5L19 19L5 19L5 5 z M 12 8C8 8 6 12 6 12C6 12 8 16 12 16C16 16 18 12 18 12C18 12 16 8 12 8 z M 12 10C13.104 10 14 10.896 14 12C14 13.104 13.104 14 12 14C10.896 14 10 13.104 10 12C10 10.896 10.896 10 12 10 z"></path></svg>',
        insertAfter: '#wmd-spacer4',
        shortcut: 'ctrl+p',
        command({target}) {
            let previewStatus = (target.getAttribute('active') || "false") === "true";
            localStorage.setItem("editor-show-preview", !previewStatus);
            $(".edit-area").attr("preview", !previewStatus);
            $("#wmd-preview-button").attr("active", !previewStatus);
        },
        onMounted(event) {
            let previewStatus = localStorage.getItem('editor-show-preview') || "true";
            $(".edit-area").attr("preview", previewStatus);
            $(event.target).attr("active", previewStatus);
        }
    }, {
        id: 'wmd-code-highlight-button',
        style: 'border: 1px solid currentColor; border-radius: 4px;padding-inline: 8px;',
        name: '<?php _e("代码高亮主题"); ?>',
        icon: '<span style="width: unset;display: flex;align-items: center;">无主题</span>',
        insertAfter: '#wmd-preview-button',
        onMounted(event) {
            let styleJson = JSON.parse('<?php echo json_encode(['off' => '关闭'] + Util::listHljsCss(), JSON_UNESCAPED_UNICODE); ?>');
            let select = $('<select>', {
                id: 'wmd-code-theme',
                class: 'wmd-select',
                style: 'height: 25px;'
            });

            $.each(styleJson, function (key, value) {
                select.append($('<option>', {
                    value: key,
                    text: value
                }));
            });

            $(event.target).replaceWith($('<li id="wmd-code-highlight-button" style="padding: 1px;">').append(select));
            $('body').on('XEditorPreviewEnd', () => {
                $('#wmd-preview pre code').toArray().forEach(el => {
                    const {hljs} = window;
                    if (typeof hljs === "object" && "highlightElement" in hljs) {
                        const copy = document.createElement('span');
                        // 还原编码
                        let div = document.createElement('div');
                        div.innerHTML = el.innerHTML;
                        copy.dataset.clipboardText = div.innerText;
                        copy.classList.add('copy');
                        copy.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path d="M6.9998 6V3C6.9998 2.44772 7.44752 2 7.9998 2H19.9998C20.5521 2 20.9998 2.44772 20.9998 3V17C20.9998 17.5523 20.5521 18 19.9998 18H16.9998V20.9991C16.9998 21.5519 16.5499 22 15.993 22H4.00666C3.45059 22 3 21.5554 3 20.9991L3.0026 7.00087C3.0027 6.44811 3.45264 6 4.00942 6H6.9998ZM5.00242 8L5.00019 20H14.9998V8H5.00242ZM8.9998 6H16.9998V16H18.9998V4H8.9998V6Z"></path></svg>`;
                        copy.setAttribute('title', '<?php _e("点击复制"); ?>')
                        el.after(copy);
                        hljs.highlightElement(el);
                        el.parentNode.classList.add('styled');
                    }
                });
                let clp = new ClipboardJS('#wmd-preview pre span.copy');
                clp.on('success', function (e) {
                    Toastify({
                        text: "<?php _e("复制成功"); ?>",
                        duration: 3000,
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)"
                        }
                    }).showToast();
                });
                clp.on('error', function (e) {
                    Toastify({
                        text: "<?php _e("复制失败"); ?>",
                        duration: 3000,
                        style: {
                            background: "linear-gradient(to right, #ff4757, #ff4757)"
                        }
                    }).showToast();
                });
            });
            let codeTheme = localStorage.getItem('editor-hljs-theme') || "off";
            if (Object.keys(styleJson).includes(codeTheme)) {
                select.val(codeTheme);
                setTheme(codeTheme);
            }

            select.change(() => {
                setTheme(select.val())
            })


            function setTheme(filename) {
                $("#editor-hljs-css").remove();
                const previewArea = $("#wmd-preview");
                if (filename && Object.keys(styleJson).includes(filename)) {
                    localStorage.setItem('editor-hljs-theme', filename)
                    previewArea.removeAttr('hljs');
                    if (filename !== "off") {
                        let linkElement = document.createElement('link');
                        linkElement.id = 'editor-hljs-css';
                        linkElement.rel = "stylesheet";
                        linkElement.type = "text/css";
                        linkElement.href = "<?php echo \TypechoPlugin\AAEditor\Util::pluginStatic('css', 'highlight.js/'); ?>" + filename;
                        document.head.appendChild(linkElement);
                        previewArea.attr("hljs", filename.replace(/.+\//, ""));
                    }

                }
            }
        }
    }, {
        id: 'wmd-fullscreen-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M8 3V5H4V9H2V3H8ZM2 21V15H4V19H8V21H2ZM22 21H16V19H20V15H22V21ZM22 9H20V5H16V3H22V9Z"></path></svg>'
    }, {
        id: 'wmd-exit-fullscreen-button',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M18 7H22V9H16V3H18V7ZM8 9H2V7H6V3H8V9ZM18 17V21H16V15H22V17H18ZM8 15V21H6V17H2V15H8Z"></path></svg>',
    }].forEach(btn => {
        $('body').trigger('XEditorAddButton', [btn]);
    });
    $('body').trigger('XEditorAddHtmlProcessor', [function (html) {
        if (html.indexOf("[hide") === -1) return html;
        const regex = /\[hide](.*?)\[\/hide]/gi;
        return html.replace(regex, `<div class="x-hide fake blur">
    <span class="x-hide-icon" title="<?php _e("显示/隐藏"); ?>" onclick="this.parentNode.classList.toggle('blur');">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><path d="M23.986328 9C12.666705 9 2.6928719 16.845918 0.046875 27.126953 A 1.5002454 1.5002454 0 0 0 2.953125 27.873047C5.2331281 19.014082 14.065951 12 23.986328 12C33.906705 12 42.767507 19.01655 45.046875 27.873047 A 1.5002454 1.5002454 0 0 0 47.953125 27.126953C45.306493 16.84345 35.305951 9 23.986328 9 z M 24.001953 17C18.681885 17 14.337891 21.343999 14.337891 26.664062C14.337891 31.984127 18.681885 36.330078 24.001953 36.330078C29.322021 36.330078 33.667969 31.984126 33.667969 26.664062C33.667969 21.343999 29.322021 17 24.001953 17 z"></path></svg>
    </span>
    <span class="x-hide-text" onclick="this.parentNode.classList.toggle('blur');">$1</span>
</div>`);
    }, 1]);
    document.addEventListener('DOMContentLoaded', () => {
        if ($('[name="markdown"]').val())
            $('body').trigger('XEditorInit', []);
    })
</script>
