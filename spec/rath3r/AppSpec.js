describe("App", function() {
  var App = require('../../assets/scripts/App');

  beforeEach(function() {
    app = new App();
  });

  it("should be initialise", function() {
    app.init();
    expect(app.initialised).toBe(true);
  });
});
