describe("App", function() {
  var expect = require('chai').expect;
  var App = require('../../assets/scripts/App');
  var app = new App();

  // beforeEach(function() {
  //   app = new App();
  // });

  it("should be initialise", function() {
    app.init();
    expect(app.initialised).to.equal(true);
  });
});
