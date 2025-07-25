<template>
    <section id="about" class="about flex flex-col items-center text-center">
        <img src="/CrispyKing.png" alt="Crispy King Logo" class="mb-4 h-20 w-20 rounded-full border-4 border-orange-100 shadow-lg" />
        <h2 class="mb-2 text-2xl font-bold text-orange-700">About Us</h2>
        <p class="max-w-xl text-base leading-relaxed text-gray-700">
            Crispy King is your go-to place for mouth-watering fried chicken, burgers, and sides. We use only the freshest ingredients and our secret
            blend of spices to bring you the best flavors in town. Whether dining in or ordering online, we promise a royal experience every time!
        </p>
    </section>
</template>

<script>
export default {
    name: 'CustomerAbout',
};
</script>

<style scoped>
.about {
    background: #fff;
    border-radius: 16px;
    padding: 2.5rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 16px rgba(255, 140, 0, 0.08);
    transition: box-shadow 0.2s;
}
.about:hover {
    box-shadow: 0 8px 24px rgba(255, 140, 0, 0.15);
}
@media (max-width: 600px) {
    .about {
        padding: 1.2rem 0.5rem;
    }
}
</style>
