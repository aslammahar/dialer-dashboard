<script type="text/javascript">



 
         
        var options = {
           series: [{{ $total_leads }}, {{ $pending_leads }}, {{ $loss_leads }}, {{ $win_leads }}],

    
          chart: {
          height: 370,
          type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            offsetY: 0,
            startAngle: 0,
            endAngle: 270,
            hollow: {
              margin: 50,
              size: '30%',
              background: 'transparent',
              image: undefined,
            },
            dataLabels: {
              name: {
                show: true,
              },
              value: {
                show: true,
              }
            }
          }
        },
        colors: ['#1ab7ea', '#FF5733', '#C70039', '#0FFF50'],
        labels: ['Total Leads', 'Pending Leads', 'Loss Leads', 'Win Leads'],
        legend: {
          show: true,
          floating: true,
          fontSize: '16px',
          position: 'left',
          offsetX: 20,
          offsetY: 2,
          labels: {
            useSeriesColors: true,
          },
          markers: {
            size: 0
          },
          formatter: function(seriesName, opts) {
            return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]
          },
          itemMargin: {
            vertical: 3
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
                show: false
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#chart1"), options);
        chart.render();








var options = {
  series: [{{ $crm_hp_lead }}, {{ $crm_mp_lead }}, {{ $crm_lp_lead }}],
  chart: {
    width: 496, 
    type: 'donut',
    dropShadow: {
      enabled: true,
      color: '#111',
      top: -1,
      left: 3,
      blur: 3,
      opacity: 0.2
    }
  },
  stroke: {
    width: 0,
  },
  plotOptions: {
    pie: {
      donut: {
        labels: {
          show: true,
          total: {
            showAlways: true,
            show: true
          }
        }
      }
    }
  },
  labels: ["High Priority", "Medium Priority", "Low Priority"],
  dataLabels: {
    dropShadow: {
      blur: 3,
      opacity: 0.8
    }
  },
  fill: {
    type: 'pattern',
    opacity: 1,
    pattern: {
      enabled: true,
      style: ['verticalLines', 'squares', 'horizontalLines', 'circles','slantedLines'],
    },
  },
  states: {
    hover: {
      filter: 'none'
    }
  },
  theme: {
    palette: 'palette2'
  },

  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
};

var chart = new ApexCharts(document.querySelector("#chart2"), options);
chart.render();






var options = {
        series: [
            {
                name: 'Total Leads',
                data: [{{ $total_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },
            {
                name: 'Win Leads',
                data: [{{ $win_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },
            {
                name: 'Pending Leads',
                data: [{{ $pending_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },
            {
                name: 'Loss Leads',
                data: [{{ $loss_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },

        ],
        chart: {
            type: 'bar',
            height: 330,
            stacked: false,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }
        ],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 10,
                dataLabels: {
                    total: {
                        enabled: true,
                        style: {
                            fontSize: '13px',
                            fontWeight: 900
                        }
                    }
                }
            }
        },
        xaxis: {
            type: 'category',
            categories: {!! json_encode($categories) !!},
            labels: {
                rotate: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Helvetica, Arial, sans-serif'
                }
            }
        },
        legend: {
            position: 'right',
            offsetY: 40
        },
        fill: {
            opacity: 1
        }
    };

    var chart = new ApexCharts(document.querySelector('#chart3'), options);
    chart.render();








var options = {
  series: [
    {
      name: 'Alice',
      data: [{{ $alice_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Avatar',
      data: [{{ $avatar_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Closer',
      data: [{{ $closer_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Avatar Office',
      data: [{{ $sellerz_avatar_office_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Avatar WFH',
      data: [{{ $sellerz_avatar_wfh_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Closers',
      data: [{{ $sellerz_closers_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Voice',
      data: [{{ $sellerz_voice_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Voice',
      data: [{{ $voice_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'WFH',
      data: [{{ $wfh_w_lead }}, 0, 0, 0, 0, 0, 0, 0]
    }
  ],
  chart: {
    type: 'bar',
    height: 330,
    stacked: true,
    toolbar: {
      show: true
    },
    zoom: {
      enabled: true
    }
  },
  responsive: [
    {
      breakpoint: 480,
      options: {
        legend: {
          position: 'bottom',
          offsetX: -10,
          offsetY: 0
        }
      }
    }
  ],
  plotOptions: {
    bar: {
      horizontal: false,
      borderRadius: 10,
      dataLabels: {
        total: {
          enabled: true,
          style: {
            fontSize: '13px',
            fontWeight: 900
          }
        }
      }
    }
  },
  xaxis: {
    type: 'category',
    categories: {!! json_encode($categories) !!},
    labels: {
      rotate: -45,
      style: {
        fontSize: '13px',
        fontFamily: 'Helvetica, Arial, sans-serif'
      }
    }
  },
  legend: {
    position: 'right',
    offsetY: 40
  },
  fill: {
    opacity: 1
  },
  colors: ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0', '#E91E63', '#673AB7', '#FF9800', '#795548', '#00BCD4']
};

var chart = new ApexCharts(document.querySelector('#chart4'), options);
chart.render();






var options = {
  series: [
    {
      name: 'Alice',
      data: [{{ $alice_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Avatar',
      data: [{{ $avatar_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Closer',
      data: [{{ $closer_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Avatar Office',
      data: [{{ $sellerz_avatar_office_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Avatar WFH',
      data: [{{ $sellerz_avatar_wfh_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Closers',
      data: [{{ $sellerz_closers_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Voice',
      data: [{{ $sellerz_voice_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Voice',
      data: [{{ $voice_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'WFH',
      data: [{{ $wfh_p_lead }}, 0, 0, 0, 0, 0, 0, 0]
    }
  ],
  chart: {
    type: 'bar',
    height: 330,
    stacked: true,
    toolbar: {
      show: true
    },
    zoom: {
      enabled: true
    }
  },
  responsive: [
    {
      breakpoint: 480,
      options: {
        legend: {
          position: 'bottom',
          offsetX: -10,
          offsetY: 0
        }
      }
    }
  ],
  plotOptions: {
    bar: {
      horizontal: false,
      borderRadius: 10,
      dataLabels: {
        total: {
          enabled: true,
          style: {
            fontSize: '13px',
            fontWeight: 900
          }
        }
      }
    }
  },
  xaxis: {
    type: 'category',
    categories: {!! json_encode($categories) !!},
    labels: {
      rotate: -45,
      style: {
        fontSize: '13px',
        fontFamily: 'Helvetica, Arial, sans-serif'
      }
    }
  },
  legend: {
    position: 'right',
    offsetY: 40
  },
  fill: {
    opacity: 1
  },
  colors: ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0', '#E91E63', '#673AB7', '#FF9800', '#795548', '#00BCD4']
};

var chart = new ApexCharts(document.querySelector('#chart5'), options);
chart.render();










var options = {
  series: [
    {
      name: 'Alice',
      data: [{{ $alice_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Avatar',
      data: [{{ $avatar_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Closer',
      data: [{{ $closer_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Avatar Office',
      data: [{{ $sellerz_avatar_office_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Avatar WFH',
      data: [{{ $sellerz_avatar_wfh_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Closers',
      data: [{{ $sellerz_closers_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Sellerz Voice',
      data: [{{ $sellerz_voice_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'Voice',
      data: [{{ $voice_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    },
    {
      name: 'WFH',
      data: [{{ $wfh_l_lead }}, 0, 0, 0, 0, 0, 0, 0]
    }
  ],
  chart: {
    type: 'bar',
    height: 330,
    stacked: true,
    toolbar: {
      show: true
    },
    zoom: {
      enabled: true
    }
  },
  responsive: [
    {
      breakpoint: 480,
      options: {
        legend: {
          position: 'bottom',
          offsetX: -10,
          offsetY: 0
        }
      }
    }
  ],
  plotOptions: {
    bar: {
      horizontal: false,
      borderRadius: 10,
      dataLabels: {
        total: {
          enabled: true,
          style: {
            fontSize: '13px',
            fontWeight: 900
          }
        }
      }
    }
  },
  xaxis: {
    type: 'category',
    categories: {!! json_encode($categories) !!},
    labels: {
      rotate: -45,
      style: {
        fontSize: '13px',
        fontFamily: 'Helvetica, Arial, sans-serif'
      }
    }
  },
  legend: {
    position: 'right',
    offsetY: 40
  },
  fill: {
    opacity: 1
  },
  colors: ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0', '#E91E63', '#673AB7', '#FF9800', '#795548', '#00BCD4']
};

var chart = new ApexCharts(document.querySelector('#chart6'), options);
chart.render();









var options = {
        series: [
            {
                name: 'Total Leads',
                data: [{{ $total_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },
            {
                name: 'Win Leads',
                data: [{{ $win_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },
            {
                name: 'Pending Leads',
                data: [{{ $pending_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },
            {
                name: 'Loss Leads',
                data: [{{ $loss_leads }}, 0, 0, 0, 0, 0, 0, 0]
            },

        ],
        chart: {
            type: 'bar',
            height: 330,
            stacked: false,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }
        ],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 10,
                dataLabels: {
                    total: {
                        enabled: true,
                        style: {
                            fontSize: '13px',
                            fontWeight: 900
                        }
                    }
                }
            }
        },
        xaxis: {
            type: 'category',
            categories: {!! json_encode($categories) !!},
            labels: {
                rotate: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Helvetica, Arial, sans-serif'
                }
            }
        },
        legend: {
            position: 'right',
            offsetY: 40
        },
        fill: {
            opacity: 1
        }
    };

    var chart = new ApexCharts(document.querySelector('#chart7'), options);
    chart.render();


    </script>
