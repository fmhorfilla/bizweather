<template>
  <div>
     <div class="wave"></div>
     <div class="wave"></div>
     <div class="wave"></div>
  </div>

      <div class="container">

        <div class="row justify-content-center pb-4 pt-4">
              <div class="col-md-7 heading-section text-center">
                  <h1>BizWeather Data</h1>
              </div>
          </div>

        <div class="search-box mb-5">
            <input
              type="text"
              v-model="searchQuery"
              class="form-control"
              placeholder="Search for a city"
            />
        </div>
          
          <div v-if="filteredGeonames.length > 0" class="row">
              <div class="col-md-2" v-for="(item, index) in filteredGeonames" :key="index">
                  <div class="block-card">
                      <div class="img"><img :src="`/icons/${item.weather_icon}.png`" alt="Weather Icon" class="weather-icon"></div>
                      <div class="text-center p-3">
                          <span class="excerpt d-block">{{ item.weather_main }}</span>
                          <span class="box"><span class="number">{{ item.name }}</span></span>
                          <ul class="pricing-text mb-2 mt-3">
                              <li>Temperature: {{ item.temperature }}°C</li>
                              <li>Humidity: {{ item.humidity }}%</li>
                              <li>Overview:<br/><span class="description">{{ item.weather_description }}</span></li>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>
          <div v-else class="container-loader">
            <span class="loader"></span>
            <div class="loader-label">Please wait.....</div>
          </div>
          
      </div>

</template>

<script>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';

export default {
  name: 'App',
  setup() {
    const geonames = ref({});
    const searchQuery = ref('');

    onMounted(async () => {
      try {
        const response = await axios.get('http://127.0.0.1:1818/api/geonames');
        geonames.value = response.data;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    });

    const filteredGeonames = computed(() => {
      const query = searchQuery.value.toLowerCase();
      return Object.values(geonames.value).filter(item =>
        item.name.toLowerCase().includes(query)
      );
    });

    return {
      geonames,
      searchQuery,
      filteredGeonames,
    };
  },
};
</script>