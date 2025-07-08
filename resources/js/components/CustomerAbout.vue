<template>
    <section id="about" class="about">
        <h2>About Us</h2>
        <p>
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
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}
@media (max-width: 600px) {
    .about {
        padding: 1rem;
    }
}
</style>
