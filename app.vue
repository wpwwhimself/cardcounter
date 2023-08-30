<script setup>
const navItems = [
  {
    label: "Kości",
    icon: "dice",
    link: "/dice",
  }
];
</script>

<template>
  <header class="flex-right v-center">
    <NuxtLink to="/">
      <img src="~/assets/logo.svg" alt="Cardcounter logo" />
    </NuxtLink>

    <nav>
      <NuxtLink v-for="item in navItems" :to="item.link">
        <div class="link flex-down v-center h-center">
          <fai class="icon" :icon="`fas fa-${item.icon}`" />
          <label>{{ item.label }}</label>
        </div>
      </NuxtLink>
    </nav>
  </header>

  <div id="main-wrapper">
    <NuxtPage />
  </div>

  <footer>
    <h2>Cardcounter</h2>
    <p>Made by WPWW</p>
  </footer>
</template>

<style scoped>
nav{
  opacity: 0;
  translate: -10px 0;
  transition-duration: 0.3s;
}
header:hover nav{
  opacity: 1;
  translate: 0;
}
img{
  width: 6em;
}
.icon{
  font-size: 2em;
}

.link{
  padding: 0.5em 1em;
  border: 2px solid var(--acc);
}
</style>