//
//  YourMissionCell.m
//  Space Flight
//
//  Created by Michael Stratford on 7/2/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "YourMissionCell.h"

@implementation YourMissionCell

@synthesize imageView;
@synthesize title;
@synthesize submitted;
@synthesize submitButton;
@synthesize playButton;
@synthesize editButton;

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

- (void)dealloc {
    [imageView release];
    [title release];
    [submitted release];
    [playButton release];
    [editButton release];
    [submitButton release];
    [super dealloc];
}

@end
