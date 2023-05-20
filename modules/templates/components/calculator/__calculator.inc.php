<section class="calculator">
    <div class="container">
        <h1 class="calculator__title title-h1">Расчёт стоимости доставки</h1>
        <form class="calculator__form form" id="form-calculator" name="calculator">
            <h2 class="form__title title-h2">Маршрут грузоперевозки</h2>
            <div class="form__block_column">
                <div class="form__cell form__cell_from">
                    <lable class="form__lable" for="from-locality">От куда</lable>
                    <div class="form__inner form__inner_region">
                        <p class="form__link">Указать регион</p>
                        <input
                            class="form__input form__input_region display_off"
                            type="text"
                            placeholder="Регион"
                            name="from-region"
                        />
                        <div class="form__hints form__hints_region"></div>
                    </div>
                    <div class="form__inner form__inner_locality">
                        <input
                            class="form__input form__input_locality"
                            type="text"
                            placeholder="Населённый пункт"
                            name="from-locality"
                            required
                        />
                        <div class="form__hints form__hints_locality"></div>
                    </div>
                </div>
                <div class="form__cell form__cell_where">
                    <lable class="form__lable" for="where-locality">Куда</lable>
                    <div class="form__inner form__inner_region">
                        <p class="form__link">Указать регион</p>
                        <input
                            class="form__input form__input_region display_off"
                            type="text"
                            placeholder="Регион"
                            name="where-region"
                        />
                        <div class="form__hints form__hints_region"></div>
                    </div>
                    <div class="form__inner form__inner_locality">
                        <input
                            class="form__input form__input_locality"
                            type="text"
                            placeholder="Населённый пункт"
                            name="where-locality"
                            required
                        />
                        <div class="form__hints form__hints_locality"></div>
                    </div>
                </div>
            </div>
            <h2 class="form__title title-h2">Параметры груза</h2>
            <div class="form__block">
                <div class="form__block">
                <div class="form__cell">
                    <lable class="form__lable" for="weight">Общий вес, кг</lable>
                    <input class="form__input" type="number" step="0.1" name="weight" required/>
                </div>
                <div class="form__cell">
                    <lable class="form__lable" for="volume"
                        >Общий объём, м<sup><small>3</small></sup></lable
                    >
                    <input class="form__input" type="number" step="0.001" name="volume" required/>
                </div>
                </div>
                <div class="form__block">
                <div class="form__cell">
                    <lable class="form__lable" for="max-size">Макс. габарит, м</lable>
                    <input class="form__input" type="number" step="0.1" name="max-size" required/>
                </div>
                <div class="form__cell">
                    <lable class="form__lable" for="quantity">Кол-во мест</lable>
                    <input class="form__input" type="number" step="1" name="quantity" required/>
                </div>
                </div>
            </div>
            <h2 class="form__title title-h2">Информация о грузе</h2>
            <div class="form__block">
                <div class="form__cell">
                    <lable class="form__lable" for="price">Объявленная стоимость, ₽</lable>
                    <input class="form__input" type="number" step="500" name="price" required/>
                </div>
                <div class="form__cell">
                    <lable class="form__lable" for="type-cargo">Характер груза</lable>
                    <input class="form__input" type="text" name="type-cargo" />
                </div>
            </div>
            <div class="form__block">
                <input class="button form__submit" type="submit" value="Расчитать">
            </div>
        </form>
        <div class="calculator__screen">
            <!-- <div class="company">
                <img class="company__img" src="/resources/images/pec.jpg">
                <div class="company__cost">4240₽</div>
                <div class="company__time">1-2 д.</div>
                <a class="company__link" href="https://pecom.ru/services-are/shipping-request/" target="_blank">
                    <div class="button company__button">Перейти на сайт компании</div>
                </a>
            </div>
            <div class="company">
                <img class="company__img" src="/resources/images/kit.jpg">
                <div class="company__cost">3474₽</div>
                <div class="company__time">1 д.</div>
                <a class="company__link" href="https://tk-kit.com/order-one" target="_blank">
                    <div class="button company__button">Перейти на сайт компании</div>
                </a>
            </div> -->
        </div>
    </div>
</section>