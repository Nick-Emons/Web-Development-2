<template>
  <div class="champion-detail" v-if="champion">
    <button class="return-button" @click="goBack">← Back to Champions List</button>
    <div class="container">
      <!-- Tabs at the top, binnen de container -->
      <div class="tabs">
        <button :class="{ active: selectedTab === 'Overview' }" @click="selectedTab = 'Overview'">Overview</button>
        <button :class="{ active: selectedTab === 'Abilities' }" @click="selectedTab = 'Abilities'">Abilities</button>
        <button :class="{ active: selectedTab === 'Skins' }" @click="selectedTab = 'Skins'">Skins</button>
      </div>

      <!-- Conditionally render left and right panel only for 'Overview' tab -->
      <div class="left-panel" v-if="selectedTab === 'Overview'">
        <div class="header">
          <div class="name-title">
            <div class="name-title-box">
              <h1>{{ champion.details.name }}</h1>
              <h2>{{ champion.details.title }}</h2>
            </div>
          </div>
        </div>

        <!-- Overview Tab Content -->
        <div>
          <h3 class="section-title">Stats:</h3>
          <ul class="champion-overview">
            <li>HP: {{ champion.details.stats.hp }}</li>
            <li>MP: {{ champion.details.stats.mp }}</li>
            <li>Armor: {{ champion.details.stats.armor }}</li>
            <li>Spell Block: {{ champion.details.stats.spellblock }}</li>
            <li>Attack Damage: {{ champion.details.stats.attackdamage }}</li>
          </ul>

          <div class="lore">
            <h3 class="section-title">Lore:</h3>
            <p>{{ champion.details.lore }}</p>
          </div>
        </div>
      </div>

      <div class="right-panel"
        :style="{ backgroundImage: `url(https://ddragon.leagueoflegends.com/cdn/img/champion/splash/${champion.details.id}_0.jpg)` }"
        v-if="selectedTab === 'Overview'">
        <!-- Background image for the splash art -->
      </div>

      <!-- Full screen content for Abilities Tab -->
      <div v-if="selectedTab === 'Abilities'" class="full-screen">
        <div class="spells-container">
          <h3 class="section-title">Abilities</h3>
          <div class="spell-cards">
            <!-- Passive Card -->
            <div class="spell-card" @click="handleClick(champion.details.passive)"
              :class="{ active: selectedAbility === champion.details.passive }">
              <img
                :src="`https://ddragon.leagueoflegends.com/cdn/12.14.1/img/passive/${champion.details.passive.image.full}`"
                :alt="`Passive Image for ${champion.details.name}`" class="spell-image" />
              <p class="spell-name">{{ champion.details.passive.name }}</p>
            </div>

            <!-- Spell Cards -->
            <div v-for="spell in champion.details.spells" :key="spell.id" class="spell-card" @click="handleClick(spell)"
              :class="{ active: selectedAbility === spell }">
              <img :src="`https://ddragon.leagueoflegends.com/cdn/12.14.1/img/spell/${spell.id}.png`"
                :alt="`Spell Image for ${spell.name}`" class="spell-image" />
              <p class="spell-name">{{ spell.name }}</p>
            </div>
          </div>
        </div>

        <!-- Description for selected ability -->
        <div v-if="selectedAbility" class="ability-description">
          <img :src="selectedAbilityImageUrl" :alt="`Image for ${selectedAbility.name}`" class="selected-ability-image" />
          <h3>{{ selectedAbility.name }}</h3>
          <p>{{ cleanedAbilityDescription }}</p>
        </div>
      </div>

      <!-- Skins tab -->
      <div class="left-panel" :class="{ skins: selectedTab === 'Skins' }" v-if="selectedTab === 'Skins'">
        <h3 class="section-title">Skins</h3>
        <div class="skin-cards-container">
          <!-- Loop through the skins and create a card for each -->
          <div v-for="skin in skins" :key="skin.id" class="skin-card" @click="selectSkin(skin)"
            :class="{ active: selectedSkin === skin }">
            <img
              :src="`https://ddragon.leagueoflegends.com/cdn/img/champion/loading/${champion.details.id}_${skin.num}.jpg`"
              :alt="`Skin Image for ${skin.name}`" class="skin-image" />
            <p>{{ skin.num === 0 ? 'Default skin' : skin.name }}</p>
          </div>
        </div>
      </div>

      <!-- Right panel -->
      <div class="right-panel" :class="{ skins: selectedTab === 'Skins' }"
        :style="{ backgroundImage: `url(https://ddragon.leagueoflegends.com/cdn/img/champion/loading/${champion.details.id}_${selectedSkin ? selectedSkin.num : 0}.jpg)` }"
        v-if="selectedTab === 'Skins'">
        <!-- Background image for the selected skin -->
      </div>
    </div>
  </div>
</template>

<script>
import api  from '../utils/axios.js'
import '../assets/ChampionDetail.css';

export default {
  data() {
    return {
      champion: null,
      selectedTab: 'Overview', // Default tab is 'Overview'
      selectedAbility: null, // Default selected ability is null
      abilityDescription: '', // Property to store the description of the selected ability
      selectedSkin: null, // Property to track the selected skin
    };
  },
  computed: {
    // Computed property om HTML-tags uit de description te verwijderen
    cleanedAbilityDescription() {
      return this.abilityDescription.replace(/<[^>]*>/g, ''); // Verwijder HTML-tags
    },
    selectedAbilityImageUrl() {
    if (!this.selectedAbility) return ''; // Controleer of er een ability is geselecteerd
    if (this.selectedAbility.image.full) {
      // Controleer of het een passive is
      if (this.selectedAbility === this.champion.details.passive) {
        return `https://ddragon.leagueoflegends.com/cdn/12.14.1/img/passive/${this.selectedAbility.image.full}`;
      }
      // Anders wordt het een spell
      return `https://ddragon.leagueoflegends.com/cdn/12.14.1/img/spell/${this.selectedAbility.image.full}`;
    }
    return ''; // Fallback als er geen image beschikbaar is
  }
  },
  mounted() {
    this.fetchChampionDetails(); // Fetch champion details when the component is mounted
  },
  methods: {
    async fetchChampionDetails() {
      const championId = this.$route.params.id;
      try {
        const response = await api.get(`/champions/${championId}`);
        this.champion = response.data;
        this.skins = this.champion.details.skins; // Vul de skins data in
        this.abilityDescription = this.champion.details.passive.description;
        // Stel de eerste skin in als de standaard geselecteerde skin
        if (this.skins.length > 0) {
          this.selectedSkin = this.skins[0]; // Dit stelt de eerste skin als default in
        }
      } catch (error) {
        console.error("Error fetching champion details:", error);
        this.champion = null;
      }
    },
    handleClick(ability) {
      this.selectedAbility = ability; // Update the selected ability on click
      // Update ability description based on the selected ability
      this.abilityDescription = ability.description || this.champion.details.passive.description;
    },
    selectSkin(skin) {
      this.selectedSkin = skin;
    },
    goBack() {
    this.$router.push('/champions'); // Replace `/champions` with your actual route
  },
  },
};
</script>


