describe("About", function() {
  var expect = require('chai').expect;
  var should = require('chai').should();
  var About = require('../../assets/scripts/About');
  var about = new About();
  global.window = window
  global.$ = require('../../node_modules/jquery/jquery');
  // beforeEach(function() {
  //   app = new App();
  // });

  it("should be initialise", function() {
    about.init();
    expect(about.initialised).to.equal(true);
  });

  it("should load a json string", function() {
    var jsonData = about.loadJson();
    // foo.should.be.a('string');
    jsonData.should.have.lengthOf(0);
    //jsonData.should.exist();
    //expect(about.loadJson).to.return([]);
  });

  it("should load a json string from an external source", function() {

  });
});
