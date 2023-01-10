<template>
    <div class="container-fluid pt-2">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Select Dates</div>

                    <div class="card-body">
                       <div class="row">
                            <div class="row">
                                <div class="col-md-12 form-group pt-2">
                                    <label for="">Start Date</label>
                                    <input type="date" class="form-control" v-model="startdate">
                                </div>
                                <div class="col-md-12 form-group pt-2">
                                    <label for="">Last Date</label>
                                    <input type="date" class="form-control" v-model="lastdate">
                                </div>
                                <div class="col-md-12 form-group align-self-end pt-2">
                                    <button class="btn btn-primary" @click.prevent="load_data()">Load </button>
                                </div>
                            </div>
                       </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Stats
                    </div>
                    <div class="card-body" >
                        <div v-if="close_data && size_data && speed_data">
                           <h3> Largest Asteroid :-  ID:{{size_data.id}} Diameter:{{size_data.diameter }}Km</h3>
                           <h3> Fastest Asteroid :-  ID:{{speed_data.id}} Speed:{{speed_data.speed }}Km/h</h3>
                           <h3> Clossest Asteroid :-  ID:{{close_data.id}} Distance:{{close_data.distance }}Km</h3>
                        </div>
                    </div>

                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        Daily Count Graph
                    </div>
                    <div class="card-body" id="chart-block">
                          <canvas id="dailycount"></canvas>

                    </div>

                </div>
                 
            </div>
        </div>
    </div>
    <vue-progress-bar></vue-progress-bar>
</template>

<script>


    export default {
        mounted() {
            console.log('Component mounted.')
        },
        name: "App",
        
        data() {
            return {
                rdata:null,
                day_count:[],
                startdate:'',
                lastdate:'',
                chartlabels:[],
                chartcount:[],
                close_data:null,
                speed_data:null,
                size_data:null,
            };
        },
        
        methods:{
           async load_data(){
              
                this.$Progress.start();
                if(this.startdate != '' && this.lastdate != ''){
                  this.chartlabels= []
                  this.chartcount= []
                  this.close_data= null
                  this.speed_data= null
                  this.size_data= null
                  this.rdata= []
                  await axios.get('https://api.nasa.gov/neo/rest/v1/feed?start_date='+this.startdate+'&end_date='+this.lastdate+'&api_key=S2ryrklixpIFQ6no0RSN1jWGpIDjv2gT7hA0TvHF').then( async(response) =>{
                        if(response.data.element_count){
                            console.log(response.data.element_count);
                            this.rdata = Object.entries(response.data.near_earth_objects) ;
                            this.rdata.sort();
                            console.log(this.rdata.sort());
                            let close_el = null
                            let current_el = null
                            this.rdata.forEach(item => {
                                this.chartlabels.push(item[0])
                                this.chartcount.push(item[1].length)
                                item[1].forEach(element => {
                                //  Calculating Close Asteroid
                                current_el =  {
                                    'distance': parseFloat(element.close_approach_data[0].miss_distance.kilometers).toFixed(2),'speed': parseFloat(element.close_approach_data[0].relative_velocity.kilometers_per_hour).   toFixed(2),
                                    'id': element.id,
                                    'diameter': ((element.estimated_diameter.kilometers.estimated_diameter_max) + (element.estimated_diameter.kilometers.estimated_diameter_max))/2
                                    }
                                if(this.close_data ==null){
                                  this.close_data =  {'distance': parseFloat(element.close_approach_data[0].miss_distance.kilometers).toFixed(2),'id': element.id}
                                }else{
                                    if(current_el.distance < this.close_data.distance){
                                        this.close_data = current_el
                                    }
                                }

                                //  Calculating Fastest Asteroid
                                if(this.speed_data ==null){
                                  this.speed_data =  {'speed': parseFloat(element.close_approach_data[0].relative_velocity.kilometers_per_hour).toFixed(2),'id': element.id}
                                }else{
                                    if(current_el.speed > this.speed_data.speed){
                                        this.speed_data = current_el
                                    }
                                }

                                //Calculating Avg Size
                                if(this.size_data ==null){
                                  this.size_data =  {'diameter': parseFloat(((element.estimated_diameter.kilometers.estimated_diameter_max) + (element.estimated_diameter.kilometers.estimated_diameter_max))/2).toFixed(2),'id': element.id}
                                }else{
                                    if(current_el.diameter > this.size_data.diameter){
                                        this.size_data = current_el
                                    }
                                }
                                

                                })

                            }); 
                            console.log(this.close_data);
                            console.log(this.speed_data);
                            console.log(this.size_data);
                            this.show_chart()                          
                        }
                        this.$Progress.finish();
                    })
                    // .catch((error)=>{
                    //     toast.fire({
                    //         'icon': 'error',
                    //         'title': 'Server Error',
                    //     });
                    // })

                }
                else{
                    toast.fire({
                        'icon': 'warning',
                        'title': 'Select Dates First',
                    });
                }
                this.$Progress.finish();

            },
            show_chart(){
                $('#dailycount').remove();
                $('#chart-block').append('<canvas id="dailycount"><canvas>');
                const ctx = document.getElementById('dailycount');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                    labels: this.chartlabels,
                    datasets: [{
                        label: 'Daily Count',
                        data: this.chartcount,
                        borderWidth: 1,
                        fill: false,
                        borderColor: '#2196f3', 
                        backgroundColor: '#2196f3', 
                        borderWidth: 3 
                    }]
                    },
                    options: {
                    scales: {
                        y: {
                            title: {
                            display: true,
                            text: 'Value'
                            },
                            
                            ticks: {
                            // forces step size to be 50 units
                            stepSize: 5
                            }
                        },
                         x: {
                            title: {
                            display: true,
                            text: 'Date'
                            },
                        }
                    }
                    }
                });
            }
        }
    }
</script>
