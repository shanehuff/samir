<template>
    <div class="champion-details flex h-screen" :style="{ backgroundImage: 'url(' + background + ')', backgroundPosition: 'center', backgroundSize: 'cover', backgroundRepeat: 'no-repeat' }">
        <!-- Display stats on the left -->
        <div class="stats left flex-1 p-4 bg-gray-200 bg-opacity-50 text-sm">
            <ul>
                <li>❤️ <HealthPointBar :remaining-h-p="champion.remain_capital" :full-h-p="champion.capital"></HealthPointBar></li>
                <li>👤 {{ champion.archetype }}</li>
                <li>🎂 {{ champion.age }}</li>
            </ul>
        </div>

        <!-- Display character name in the middle -->
        <div class="name flex-1 p-4 text-center relative">
            <div class="absolute inset-0 bg-black opacity-40"></div>
            <h1 class="text-2xl font-bold text-white absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">{{ champion.name }}</h1>
            <!-- Add other character details as needed -->
        </div>

        <!-- Display more stats on the right -->
        <div class="stats right flex-1 p-4 bg-gray-300 bg-opacity-50 text-sm">
            <ul>
                <li>💼 ${{ champion.onduty }}</li>
                <li>💪 {{ champion.roi }}</li>
                <li>🏺 ${{ champion.profit }}</li>
            </ul>
        </div>
    </div>
</template>

<script>
import HealthPointBar from "./Partials/HealthPointBar.vue";
export default {
    components: {
        HealthPointBar
    },

    props: {
        champion: {
            type: Object,
            required: true,
        },
        background: {
            type: String,
            required: true,
        },
    },
};
</script>
