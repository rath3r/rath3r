'use strict';
var expect = require('chai').expect;
var Main = require('../scripts/main.js');

//var expected = 'https://farm2.staticflickr.com/1577/24770505034_31a9986429_b.jpg';

describe('Main', function() {
  it('should exist', function() {
    expect(Main).to.not.be.undefined;
  });

  it('should initialise the page', function() {
    var app = App.init();
    expect(app).to.be.true;
  });
});
