<script setup lang="ts">
</script>

<template>
    <div class="tooltip-005">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12 0C8.3 0 5.9 1.5 4 4.3c-.4.5-.3 1.1.2 1.5l2 1.5c.5.4 1.2.3 1.6-.2 1.2-1.5 2-2.3 3.9-2.3 1.4 0 3.2.9 3.2 2.3 0 1.1-.9 1.6-2.3 2.4-1.7.9-3.9 2.1-3.9 5v.5c0 .6.5 1.1 1.1 1.1h3.4c.6 0 1.1-.5 1.1-1.1v-.3c0-2 5.9-2.1 5.9-7.5C20.2 3.1 16 0 12 0zm-.4 17.5c-1.8 0-3.2 1.5-3.2 3.2 0 1.8 1.5 3.2 3.2 3.2s3.2-1.5 3.2-3.2-1.5-3.2-3.2-3.2z"
                fill="#fff"/>
        </svg>
        <p>ツールチップの内容</p>
    </div>
</template>

<style lang="scss" scoped>
.tooltip-005 {
    display: inline-block;
    position: relative;
}

.tooltip-005 svg {
    width: 20px;
    height: 20px;
    padding: 5px;
    border-radius: 50%;
    box-sizing: border-box;
    background-color: #c6cdd3;
    cursor: pointer;
}

.tooltip-005 p {
    display: flex;
    justify-content: center;
    visibility: hidden;
    opacity: 0;
    position: absolute;
    bottom: -43px;
    left: 50%;
    transform: translateX(-50%);
    padding: .5em 1em;
    border-radius: 3px;
    background-color: #00000099;
    color: #fff;
    font-size: .7em;
    white-space: nowrap;
    transition: opacity .3s;
}

.tooltip-005:hover > p {
    visibility: visible;
    opacity: 1;
}

.tooltip-005 p::before {
    position: absolute;
    top: -6px;
    width: 9px;
    height: 6px;
    background-color: inherit;
    clip-path: polygon(50% 0, 0 100%, 100% 100%);
    content: '';
}
</style>
