<template>
    <div class="hp-bar shadow">
        <div class="hp-bar-fill" :style="{ width: fillPercentage }"></div>
        <div class="hp-bar-text">
            {{ Math.round(remainingHP / fullHP) }} %
        </div>
    </div>
</template>

<script>
export default {
    props: {
        fullHP: {
            type: Number,
            required: true,
        },
        remainingHP: {
            type: Number,
            required: true,
        },
    },
    computed: {
        fillPercentage() {
            return (this.remainingHP / this.fullHP) * 100 + "%";
        },
    },
};
</script>

<style scoped>
.hp-bar {
    width: 80%;
    height: 20px;
    background-color: #ccc;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 10px;
}

.hp-bar-fill {
    height: 100%;
    background-color: #4caf50;
    transition: width 0.3s ease;
}

.hp-bar-text {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    color: #fff;
    font-weight: bold;
}
</style>
