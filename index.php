<!DOCTYPE html>
<html lang="en" class="no-js" >
<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escuela de Formación Biblica</title>
    <link rel="icon" type="image/png" href="images/EFB.png">

    <script>
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
    </script>

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/mystyle.css">
    <link rel="stylesheet" href="css/movil.css">


    <!-- favicons
    ================================================== -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">
    <!-- Librería de iconos Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body id="top">
    
    <!-- preloader
    ================================================== -->
    <div id="preloader">
        <div id="loader" class="dots-fade">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- page wrap
    ================================================== -->
    <div id="page" class="s-pagewrap ss-home">


        <!-- # site header 
        ================================================== -->
        <header class="s-header">

            <div class="container s-header__content">
                
                <div class="s-header__block">
                    <div class="header-logo">
                        <a class="logo" href="index.php">
                            <img src="images/EFB.png" alt="logoEFB">
                        </a>
                    </div>
                    <a class="header-menu-toggle" href="#0"><span>Etapas</span></a>
                </div> <!-- end s-header__block -->
            
                <nav class="header-nav">    
                    <ul class="header-nav__links">
                        <li class="current"><a class="smoothscroll" href="#inicio">inicio</a></li>
                        <li><a class="smoothscroll" href="#nosotros">Nosotros</a></li>
                        <li><a class="smoothscroll" href="#programas">Etapas de Formación</a></li>
                        <li><a class="smoothscroll" href="#galeria">Momentos</a></li>
                        <li><a href="inscripcion.php" style="color: #ffffff; font-weight: bold;">Inscripciones</a></li>
                        <li>
                        <a href="login.php" 
                        style="color: #0976fc; font-weight: 500; padding: 0.5rem 1rem; border-radius: 4px; transition: all 0.3s ease;" 
                        onmouseover="this.style.background='rgba(243, 246, 246, 0.13)'; this.style.color='#0033ff';" 
                        onmouseout="this.style.background='transparent'; this.style.color='#e0e0e0';">
                        Iniciar Sesión</a></li>
                      <!-- Tu línea 81 termina aquí: Iniciar Sesión</a></li> -->

            <!-- NUESTRO BOTÓN INTEGRADO COMO ELEMENTO DE LA LISTA -->
            <li class="custom-header-scroll-item">
                <div class="intro-scroll">
                    <a class="smoothscroll" href="#programas">
                        <span class="intro-scroll__circle-text"></span>
                        <span class="intro-scroll__text u-screen-reader-text">Palabra de Dios</span>
                        <div class="intro-scroll__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="header-bible-svg">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                <path d="M12 7v8"></path>
                                <path d="M10 9h4"></path>
                            </svg>
                        </div>
                        <div class="intro-scroll__icon"></div>
                    </a>
                </div>
            </li>

        </ul> <!-- end header-nav__links -->
    </nav> <!-- end header-nav -->

</div> <!-- end s-header__content -->

        </header> <!-- end s-header -->


        <!-- # intro
        ================================================== -->
        <section id="etapasdeformacion" class="container s-intro target-section">

            <div class="grid-block s-intro__content">

                <div class="intro-header">
                    <div class="intro-header__overline">Bienvenidos a la Escuela de </div>
                    <h2 class="intro-header__big-type">
                        Formación<br>
                        Bíblica
                    </h2>
                </div> <!-- end intro-header -->

                <figure class="intro-pic-primary">
                    <img src="images/iprl.png" 
                         srcset="images/iprl.png 1x, 
                         images/iprl.png 2x" alt="">  
                </figure> <!-- end intro-pic-primary -->    
                    
                <div class="intro-block-content">

                    <figure class="intro-block-content__pic">
                        <img src="images/idc_prc.png" 
                             srcset="images/idc_prc.png 1x, 
                             images/idc_prc.png 2x" alt=""> 
                    </figure> <!-- end intro-pic-secondary -->   

                    <div class="intro-block-content__text-wrap">
                        <p class="intro-block-content__text">
                            "La Escuela de Formación Bíblica (EFB) es una institución educativa que tiene como objetivo formar líderes cristianos"
                        </p>

        <ul class="intro-block-content__social">
        <li><a href="https://www.facebook.com/IDCSEDEPPALCARACAS" target="_blank"><i class="fa-brands fa-facebook" style="margin-right: 15px; font-size: 2rem;"></i></a></li>
        <li><a href="https://www.instagram.com/idcccs" target="_blank"><i class="fa-brands fa-instagram" style="margin-right: 15px; font-size: 2rem;"></i></a></li>
        <li><a href="https://wa.me/1234567890" target="_blank"><i class="fa-brands fa-whatsapp" style="margin-right: 15px; font-size: 2rem;"></i></a></li>
        <li><a href="https://youtube.com/@idc-caracassedeprincipal5451" target="_blank"><i class="fa-brands fa-youtube" style="font-size: 2rem;"></i></a></li>
        </div> <!-- end intro-block-content__text-wrap -->


            </div> <!-- grid-block -->            

        </section> <!-- end s-intro -->


        <!-- # about
        ================================================== -->
        <section id="nosotros" class="container s-about target-section">

            <div class="row s-about__content">

                <div class="column xl-12 lg-12 md-12 s-about__content-start">

                    <div class="section-header" data-num="01">
                        <h2 class="text-display-title">Nuestra Historia</h2>
                    </div>  

                <div class="about-video-wrapper">
            <video autoplay loop muted playsinline class="about-video-element">
            <source src="images/historia.mp4" type="video/mp4">
        Tu navegador no soporta la reproducción de videos.
            </video>
        </div>
                </div> <!-- end s-about__content-start -->

                <div class="column xl- lg-6 md-4 s-about__content-end">                  

                    <p>
                     "Fundada en 1985 en El Paraíso, Caracas, la Escuela de Formación Bíblica (EFB) nació con el propósito firme de edificar a cada creyente a través de la Palabra de Dios. Tras una profunda reorganización a finales de los 90 que modernizó su enseñanza y estructuró las clases por rangos de edad, la institución se ha consolidado como un pilar fundamental de crecimiento espiritual, superando retos logísticos para continuar con su misión: formar líderes cristianos comprometidos."
                    </p>

                </div> <!--end column -->
                
            </div> <!-- end s-about__content-end -->
            
        </section> <!-- end s-about --> 
        
        <!-- # Programas
        ================================================== -->
        <section id="programas" class="container s-programs target-section">

           <div class="row s-programs__content">
    <div class="column xl-12 s-programs__content-start">
        <div class="section-header" data-num="03">
            <h2 class="text-display-title">Etapas de Formación</h2>
        </div>
    </div>
</div>

                <section id="etapasdeformacion" class="container s-programs target-section">

                <div class="row s-programs__blocks grid-cols grid-cols--wrap custom-programs-container">
    
            <div class="grid-cols__column column xl-4 lg-6 md-12 s-programs__block">
            <h3 class="programs-block__title">Etapas 1</h3>
            <p class="intro-block-content__text">Consta de 3 niveles</p>
            <ul class="programs-list">
            <li class="program-item" data-desc="En este nivel inicial aprenderás los fundamentos doctrinales del bautismo, la importancia del pacto público de fe y los primeros pasos en tu caminar con Cristo.">1ero A: Escuela para Bautismo</li>
            <li class="program-item" data-desc="Un estudio profundo sobre la naturaleza, el propósito y la misión de la iglesia local y universal, entendiendo nuestro rol en el cuerpo de Jesucristo.">1ero B: Iglesia de Jesucristo</li>
            <li class="program-item" data-desc="Desarrolla el entendimiento de la autoridad espiritual delegada por Dios y la búsqueda activa de una vida en santidad e integridad delante del Señor.">1ero C: Autoridad y Santidad</li>
        </ul>
    </div>

    <div class="grid-cols__column column xl-4 lg-6 md-12 s-programs__block">
        <h3 class="programs-block__title">Etapas 2</h3>
        <p class="intro-block-content__text">Consta de 3 niveles</p>
        <ul class="programs-list">
            <li class="program-item" data-desc="Herramientas prácticas y fundamentos bíblicos para compartir el evangelio de manera efectiva, perdiendo el temor y cumpliendo la Gran Comisión.">2do A: Como Evangelizar</li>
            <li class="program-item" data-desc="Descubre el llamado de Dios para tu vida y cómo liderar ministerios o grupos con un enfoque claro en el servicio, la visión y el propósito divino.">2do B: Formando Lideres con Proposito</li>
            <li class="program-item" data-desc="Enfoque en el fruto del Espíritu Santo. Cómo forjar un carácter maduro, resistente a las pruebas y alineado con los principios de las Escrituras.">2do C: Formando Caracter</li>
        </ul>
    </div>

    <div class="grid-cols__column column xl-4 lg-6 md-12 s-programs__block">
        <h3 class="programs-block__title">Etapas 3</h3>
        <p class="intro-block-content__text">Consta de 4 niveles</p>
        <ul class="programs-list">
            <li class="program-item" data-desc="Principios avanzados de liderazgo eclesiástico, gestión de equipos y el desarrollo de una vida espiritual ejemplar para guiar a otros.">3ro A: Liderazgo</li>
            <li class="program-item" data-desc="Capacitación para el cuidado pastoral, la retención de nuevos creyentes y la consejería bíblica para restaurar vidas y familias.">3ro B: Consolidacion y Consejeria</li>
            <li class="program-item" data-desc="Un viaje teológico para comprender el origen de la Biblia, su inspiración divina, infalibilidad y cómo interpretarla correctamente.">3ro C: Naturaleza de la biblia</li>
            <li class="program-item" data-desc="Recorrido histórico desde la iglesia primitiva en el libro de Hechos hasta la actualidad, analizando los avivamientos y la preservación de la sana doctrina.">4to: Historia de la Iglesia</li>
        </ul>
    </div>

</div>

<div class="row description-container">
    <div class="column xl-12">
        <div id="program-description-box" class="description-box">
            <p id="description-text">💡 <em>Haz clic en cualquiera de los niveles de arriba para ver el detalle del programa aquí abajo.</em></p>
        </div>
    </div>
</div>
        </section> <!-- end s-programs -->






        <!-- # Momentos
        ================================================== -->
        <section id="galeria" class="container s-gallery target-section">

            <div class="row s-gallery__header">
                <div class="column xl-12 section-header-wrap">
                    <div class="section-header" data-num="03">
                        <h2 class="text-display-title">Momentos</h2>
                    </div>               
                </div> <!-- end section-header-wrap -->   
            </div> <!-- end s-momentos__header -->   

            <div class="gallery-items grid-cols grid-cols--wrap">

                <div class="gallery-items__item grid-cols__column">
                    <a href="images/1a.jpeg" class="gallery-items__item-thumb glightbox">
                        <img src="images/1a.jpeg" 
                            srcset="images/1a.jpeg 1x, 
                                    images/1a.jpeg 2x" alt="">                                
                    </a>
                </div> <!-- end gallery-items__item-->
    
                <div class="gallery-items__item grid-cols__column">
                    <a href="images/1b.jpeg" class="gallery-items__item-thumb glightbox">
                        <img src="images/1b.jpeg" 
                            srcset="images/1b.jpeg 1x, 
                                    images/1b.jpeg 2x" alt="">
                    </a>
                </div> <!-- end gallery-items__item -->
    
                <div class="gallery-items__item grid-cols__column">
                    <a href="images/gallery/large/l-gallery-03.jpg" class="gallery-items__item-thumb glightbox">
                        <img src="images/gallery/gallery-03.jpg" 
                            srcset="images/gallery/gallery-03.jpg 1x, 
                                    images/gallery/gallery-03@2x.jpg 2x" alt="">
                    </a>
                </div> <!-- end gallery-items__item -->
    
                <div class="gallery-items__item grid-cols__column">
                    <a href="images/gallery/large/l-gallery-04.jpg" class="gallery-items__item-thumb glightbox">
                        <img src="images/gallery/gallery-04.jpg" 
                            srcset="images/gallery/gallery-04.jpg 1x, 
                                    images/gallery/gallery-04@2x.jpg 2x" alt="">
                    </a>
                </div> <!-- end gallery-items__item -->
    
                <div class="gallery-items__item grid-cols__column">
                    <a href="images/gallery/large/l-gallery-05.jpg" class="gallery-items__item-thumb glightbox">
                        <img src="images/gallery/gallery-05.jpg" 
                            srcset="images/gallery/gallery-05.jpg 1x, 
                                    images/gallery/gallery-05@2x.jpg 2x" alt="">
                    </a>
                </div> <!-- end gallery-items__item -->
    
                <div class="gallery-items__item grid-cols__column">
                    <a href="images/gallery/large/l-gallery-06.jpg" class="gallery-items__item-thumb glightbox">
                        <img src="images/gallery/gallery-06.jpg" 
                            srcset="images/gallery/gallery-06.jpg 1x, 
                                    images/gallery/gallery-06@2x.jpg 2x" alt="">
                    </a>
                </div> <!-- end gallery-items__item -->
    
                <div class="gallery-items__item grid-cols__column">
                    <a href="images/gallery/large/l-gallery-07.jpg" class="gallery-items__item-thumb glightbox">
                        <img src="images/gallery/gallery-07.jpg" 
                            srcset="images/gallery/gallery-07.jpg 1x, 
                                    images/gallery/gallery-07@2x.jpg 2x" alt="">
                    </a>
                </div> <!-- end gallery-items__item -->
    
                <div class="gallery-items__item grid-cols__column">
                    <a href="images/gallery/large/l-gallery-08.jpg" class="gallery-items__item-thumb glightbox">
                        <img src="images/gallery/gallery-08.jpg" 
                            srcset="images/gallery/gallery-08.jpg 1x, 
                                    images/gallery/gallery-08@2x.jpg 2x" alt="">
                    </a>
                </div> <!-- end gallery-items__item -->
                
            </div> <!-- end grid-list-items -->

        </section> <!-- end s-gallery -->  


        <!-- # testimonials
        ================================================== -->
        <section id="testimonials" class="container s-testimonials">

            <div class="row s-testimonials__content">
                <div class="column xl-12">

                    <h3 class="testimonials-title u-text-center">Testimonios</h3>
    
                    <div class="swiper-container testimonials-slider">    
                        <div class="swiper-wrapper">

                            <div class="testimonials-slider__slide swiper-slide">
                                <div class="testimonials-slider__author">
                                    <img src="images/avatars/user-02.jpg" alt="Author image" class="testimonials-slider__avatar">
                                    <cite class="testimonials-slider__cite">
                                        John Rockefeller
                                        <span>Cleveland, Ohio</span>
                                    </cite>
                                </div>
                                <p>
                                Molestiae incidunt consequatur quis ipsa autem nam sit enim magni. Voluptas tempore rem. 
                                Explicabo a quaerat sint autem dolore ducimus ut consequatur neque. Nisi dolores quaerat fuga rem nihil nostrum.
                                Laudantium quia consequatur molestias.
                                </p>
                            </div> <!-- end testimonials-slider__slide -->
            
                            <div class="testimonials-slider__slide swiper-slide">
                                <div class="testimonials-slider__author">
                                    <img src="images/avatars/user-03.jpg" alt="Author image" class="testimonials-slider__avatar">
                                    <cite class="testimonials-slider__cite">
                                        Andrew Carnegie
                                        <span>Pittsburgh, Pennsylvania</span>
                                    </cite>
                                </div>
                                <p>
                                Excepturi nam cupiditate culpa doloremque deleniti repellat. Veniam quos repellat voluptas animi adipisci.
                                Nisi eaque consequatur. Voluptatem dignissimos ut ducimus accusantium perspiciatis.
                                Quasi voluptas eius distinctio. Atque eos maxime.
                                </p>
                            </div> <!-- end testimonials-slider__slide -->
            
                            <div class="testimonials-slider__slide swiper-slide">
                                <div class="testimonials-slider__author">
                                    <img src="images/avatars/user-01.jpg" alt="Author image" class="testimonials-slider__avatar">
                                    <cite class="testimonials-slider__cite">
                                        John Morgan
                                        <span>New York City</span>
                                    </cite>
                                </div>
                                <p>
                                Repellat dignissimos libero. Qui sed at corrupti expedita voluptas odit. Nihil ea quia nesciunt. Ducimus aut sed ipsam.  
                                Autem eaque officia cum exercitationem sunt voluptatum accusamus. Quasi voluptas eius distinctio.
                                Voluptatem dignissimos ut.
                                </p>
                            </div> <!-- end testimonials-slider__slide -->
    
                            <div class="testimonials-slider__slide swiper-slide">
                                <div class="testimonials-slider__author">
                                    <img src="images/avatars/user-06.jpg" alt="Author image" class="testimonials-slider__avatar">
                                    <cite class="testimonials-slider__cite">
                                        Henry Ford
                                        <span>Dearborn, Michigan</span>
                                    </cite>
                                </div>
                                <p>
                                Nunc interdum lacus sit amet orci. Vestibulum dapibus nunc ac augue. Fusce vel dui. In ac felis 
                                quis tortor malesuada pretium. Curabitur vestibulum aliquam leo. Qui sed at corrupti expedita voluptas odit. 
                                Nihil ea quia nesciunt. Ducimus aut sed ipsam.
                                </p>
                            </div> <!-- end testimonials-slider__slide -->
        
                        </div> <!-- end swiper-wrapper -->
    
                        <div class="swiper-pagination"></div>
    
                    </div> <!-- end testimonials-slider -->
    
                </div> <!-- end column -->
            </div> <!-- end s-testimonials__content -->

        </section> <!-- end s-testimonials --> 

    </div> <!-- end page -->

    <!-- Java Script
    ================================================== -->
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

</body>
</html>