<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

namespace IO\Elementor;

class Slider extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'io_slider_widget';
    }

    public function get_title()
    {
        return esc_html__('IO Slider', 'io');
    }

    public function get_icon()
    {
        return 'eicon-slides';
    }

    public function get_categories()
    {
        return ['basic'];
    }

    public function get_keywords()
    {
        return ['slider', 'carousel', 'posts'];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'elementor-addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'query_post_type',
            [
                'label' => esc_html__('Post type', 'elementor-addon'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'post',
                'description' => esc_html__('Enter "post", "page" or custom post types name.')
            ]
        );
        $this->add_control(
            'query_post_quantity',
            [
                'label' => esc_html__('Quantity', 'elementor-addon'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => '-1',
                'description' => esc_html__('Enter the number of items to retrieve. Use -1 to get all items.')
            ]
        );
        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order by', 'elementor-addon'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'ID',
                'options' => [
                    'ID' => esc_html__('Post ID', 'elementor'),
                    'author' => esc_html__('Author', 'elementor'),
                    'title' => esc_html__('Title', 'elementor'),
                    'name' => esc_html__('Slug', 'elementor'),
                    'type' => esc_html__('Post type', 'elementor'),
                    'date' => esc_html__('Published date', 'elementor'),
                    'modified' => esc_html__('Last modified date', 'elementor'),
                    'parent' => esc_html__('Post/page parent ID', 'elementor'),
                    'comment_count' => esc_html__('Number of comments', 'elementor'),
                    'rand' => esc_html__('Random', 'elementor'),
                    'none' => esc_html__('No order', 'elementor'),
                ],
            ]
        );
        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'elementor-addon'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => esc_html__('Ascendent', 'elementor'),
                    'ASC' => esc_html__('Descendent', 'elementor'),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Slide', 'elementor-addon'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'template',
            [
                'label' => esc_html__('Template or content', 'elementor-addon'),
                'type' => \Elementor\Controls_Manager::CODE,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'description' => esc_html__('You can use {@ID}, {@post_title}, {@post_thumbnail}, {@post_content}, {@the_title} and {@the_content}. If PODS plugin is enabled you can use magic tags and other fields just as you do in PODS templates.', 'elementor-addon')
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_swiper',
            [
                'label' => esc_html__('Slider options', 'elementor'),
            ]
        );

        $slides_to_show = range(1, 10);
        $slides_to_show = array_combine($slides_to_show, $slides_to_show);

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => esc_html__('Slides to Show', 'elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__('Default', 'elementor'),
                ] + $slides_to_show,
                'frontend_available' => true,
                'render_type' => 'template',
                'selectors' => [
                    '{{WRAPPER}}' => '--e-image-carousel-slides-to-show: {{VALUE}}',
                ],
            ]
        );

        // $this->add_responsive_control(
        //     'slides_to_scroll',
        //     [
        //         'label' => esc_html__('Slides to Scroll', 'elementor'),
        //         'type' => \Elementor\Controls_Manager::SELECT,
        //         'description' => esc_html__('Set how many slides are scrolled per swipe.', 'elementor'),
        //         'options' => [
        //             '' => esc_html__('Default', 'elementor'),
        //         ] + $slides_to_show,
        //         'condition' => [
        //             'slides_to_show!' => '1',
        //         ],
        //         'frontend_available' => true,
        //     ]
        // );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__('Autoplay', 'elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' => esc_html__('Yes', 'elementor'),
                    'no' => esc_html__('No', 'elementor'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__('Pause on Hover', 'elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' => esc_html__('Yes', 'elementor'),
                    'no' => esc_html__('No', 'elementor'),
                ],
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'pause_on_interaction',
            [
                'label' => esc_html__('Pause on Interaction', 'elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' => esc_html__('Yes', 'elementor'),
                    'no' => esc_html__('No', 'elementor'),
                ],
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'frontend_available' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        // Loop requires a re-render so no 'render_type = none'
        $this->add_control(
            'infinite',
            [
                'label' => esc_html__('Infinite Loop', 'elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'true' => esc_html__('Yes', 'elementor'),
                    'false' => esc_html__('No', 'elementor'),
                ],
                'frontend_available' => true,
            ]
        );
		$this->add_control(
			'speed',
			[
				'label' => esc_html__( 'Animation Speed', 'elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 300,
				'frontend_available' => true,
                'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'direction',
			[
				'label' => esc_html__( 'Direction', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'ltr',
				'options' => [
					'ltr' => esc_html__( 'Left', 'elementor' ),
					'rtl' => esc_html__( 'Right', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'overflow',
			[
				'label' => esc_html__( 'Overflow', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'hidden',
				'options' => [
					'' => esc_html__( 'Default', 'elementor' ),
					'hidden' => esc_html__( 'Hidden', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--overflow: {{VALUE}}',
				],
			]
		);
        $this->add_control(
			'autoheight',
			[
				'label' => esc_html__( 'Autoheight', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'false',
				'options' => [
					'true' => esc_html__( 'Yes', 'elementor' ),
					'false' => esc_html__( 'No', 'elementor' ),
				],
                'description' => esc_html__( 'Slider will adapt its height to the height of the currently active slide', 'elementor' ),
			]
		);

        $this->end_controls_section();

        // Content Tab End


        // Style Tab Start

        $this->start_controls_section(
            'section_spacing',
            [
                'label' => esc_html__('Spacing', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'spacebetween',
			[
				'label' => esc_html__( 'Spacing', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 20,
				],
				'condition' => [
					'slides_to_show!' => '1',
				],
				'frontend_available' => true,
				'separator' => 'after',
			]
		);


        $this->end_controls_section();
        $this->start_controls_section(
            'section_buttons_style',
            [
                'label' => esc_html__('Next and previous button', 'elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
		$this->add_control(
			'navigation',
			[
				'label' => esc_html__( 'Navigation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => esc_html__( 'Arrows and Dots', 'elementor' ),
					'arrows' => esc_html__( 'Arrows', 'elementor' ),
					'dots' => esc_html__( 'Dots', 'elementor' ),
					'none' => esc_html__( 'None', 'elementor' ),
				],
				'frontend_available' => true,
			]
		);

        $this->add_control(
            'navigation_previous_icon',
            [
                'label' => esc_html__('Previous Arrow Icon', 'elementor'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'skin' => 'inline',
                'label_block' => false,
                'skin_settings' => [
                    'inline' => [
                        'none' => [
                            'label' => 'Default',
                            'icon' => 'eicon-chevron-left',
                        ],
                        'icon' => [
                            'icon' => 'eicon-star',
                        ],
                    ],
                ],
                'recommended' => [
                    'fa-regular' => [
                        'arrow-alt-circle-left',
                        'caret-square-left',
                    ],
                    'fa-solid' => [
                        'angle-double-left',
                        'angle-left',
                        'arrow-alt-circle-left',
                        'arrow-circle-left',
                        'arrow-left',
                        'caret-left',
                        'caret-square-left',
                        'chevron-circle-left',
                        'chevron-left',
                        'long-arrow-alt-left',
                    ],
                ],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'navigation',
							'operator' => '=',
							'value' => 'both',
						],
						[
							'name' => 'navigation',
							'operator' => '=',
							'value' => 'arrows',
						],
					],
				],
            ]
        );

        $this->add_control(
            'navigation_next_icon',
            [
                'label' => esc_html__('Next Arrow Icon', 'elementor'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'skin' => 'inline',
                'label_block' => false,
                'skin_settings' => [
                    'inline' => [
                        'none' => [
                            'label' => 'Default',
                            'icon' => 'eicon-chevron-right',
                        ],
                        'icon' => [
                            'icon' => 'eicon-star',
                        ],
                    ],
                ],
                'recommended' => [
                    'fa-regular' => [
                        'arrow-alt-circle-right',
                        'caret-square-right',
                    ],
                    'fa-solid' => [
                        'angle-double-right',
                        'angle-right',
                        'arrow-alt-circle-right',
                        'arrow-circle-right',
                        'arrow-right',
                        'caret-right',
                        'caret-square-right',
                        'chevron-circle-right',
                        'chevron-right',
                        'long-arrow-alt-right',
                    ],
                ],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'navigation',
							'operator' => '=',
							'value' => 'both',
						],
						[
							'name' => 'navigation',
							'operator' => '=',
							'value' => 'arrows',
						],
					],
				],
            ]
        );

        $this->add_control(
			'arrows_position',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => [
					'inside' => esc_html__( 'Inside', 'elementor' ),
					'outside' => esc_html__( 'Outside', 'elementor' ),
				],
				'prefix_class' => 'elementor-arrows-position-',
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .io-swiper-button-prev, {{WRAPPER}} .io-swiper-button-next' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}' => '--navigation-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

        $this->add_control(
            'buttons_color',
            [
                'label' => esc_html__('Color', 'elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .io-swiper-button-next, {{WRAPPER}} .io-swiper-button-prev' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .io-swiper-button-next svg, {{WRAPPER}} .io-swiper-button-prev svg' => 'fill: {{VALUE}};',
                ],
            ]
        );
        
		$this->add_control(
			'heading_style_dots',
			[
				'label' => esc_html__( 'Pagination', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
					'outside' => esc_html__( 'Outside', 'elementor' ),
					'inside' => esc_html__( 'Inside', 'elementor' ),
				],
				'prefix_class' => 'elementor-pagination-position-',
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_inactive_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					// The opacity property will override the default inactive dot color which is opacity 0.2.
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'background: {{VALUE}}; opacity: 1',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => esc_html__( 'Active Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);


        // Style Tab End

    }


    /**
     * Render slider widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $slider_settings = array_replace_recursive($settings, [
            "post_type" => isset($settings["query_post_type"]) ? trim($settings["query_post_type"]) : "post",
            "posts_per_page" => isset($settings["query_post_quantity"]) && intval($settings["query_post_quantity"]) ? intval($settings["query_post_quantity"]) : -1,
            "orderby" => $settings["orderby"] ? $settings["orderby"] : "post_date",
            "order" => $settings["order"] ? $settings["order"] : "DESC",

            "loop" => $settings["infinite"],
            "spacebetween" => is_array($settings["spacebetween"])&&isset($settings["spacebetween"]["size"])&&isset($settings["spacebetween"]["unit"]) ? $settings["spacebetween"]["size"] : "0",
            "slidesperview" => $settings["slides_to_show"],
        ]);

        echo \IO\IO_Slider::get_instance()->render($slider_settings);
        return;
    }
}