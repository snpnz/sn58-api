<form class="mt-4 pt-4" method="POST" action="/registration/?event=<?=$_GET['event']?>">
    <div class="mb-3 row">
        <label for="surname" class="col-sm-3 col-form-label text-md-end text-start ">Фамилия</label>
        <div class="col-sm-9">
            <input
                type="text"
                autofocus
                required
                autocomplete="family-name"
                class="form-control"
                id="surname"
                name="surname"
                pattern="[А-Я][а-я]+"
                value="<?=$_POST['surname']?>"
            >
        </div>
    </div>

      <div class="mb-3 row">
        <label for="name" class="col-sm-3 col-form-label text-md-end text-start">Имя</label>
          <div class="col-sm-9">
            <input
              type="text"
              required
              autocomplete="given-name"
              class="form-control"
              id="name"
              name="name"
              pattern="[А-Я][а-я]+\s?-?\s?([А-Я][а-я]+)?"
              value="<?=$_POST['name']?>"
            >
          </div>
        </div>

        <div class="mb-3 row">
          <label for="email" class="col-sm-3 col-form-label text-md-end text-start">E-mail</label>
          <div class="col-sm-9">
            <input
              type="email"
              required
              autocomplete="email"
              class="form-control"
              id="email"
              name="email"
              value="<?=$_POST['email']?>"
            >
          </div>
        </div>
        <div class="row">
          <div class="col-sm-3  col-form-label "></div>
          <div class="col-sm-9 text-start">
          <button type="submit" class="btn btn-outline-primary">
          <?php echo isset($_GET['invite']) ? 'Зарегистрироваться': 'Записаться'; ?>
          </button>
          </div>
        </div>
      </form>